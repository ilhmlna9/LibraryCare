<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatusHistory;
use App\Models\IntegrationSetting;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\GoogleFormsService;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IntegrationWebhookController extends Controller
{
    protected GoogleFormsService $googleFormsService;
    protected TelegramService $telegramService;
    protected WhatsAppService $whatsAppService;

    public function __construct(
        GoogleFormsService $googleFormsService,
        TelegramService $telegramService,
        WhatsAppService $whatsAppService
    ) {
        $this->googleFormsService = $googleFormsService;
        $this->telegramService = $telegramService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Webhook Endpoint untuk Google Forms / Google Sheets Apps Script
     * URL: POST /integration/google-forms
     * Header: X-API-KEY: {key}
     */
    public function googleForms(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-API-KEY', $request->query('api_key', $request->input('api_key')));
        $data = $request->all();

        Log::info('Google Forms Webhook Received', ['payload' => $data]);

        $result = $this->googleFormsService->processSubmission($data, $apiKey);

        return response()->json($result, $result['status_code'] ?? 200);
    }

    /**
     * Webhook Endpoint untuk Telegram Bot
     * URL: POST /integration/telegram/webhook
     */
    public function telegramWebhook(Request $request): JsonResponse
    {
        $update = $request->all();
        Log::info('Telegram Webhook Received', ['update' => $update]);

        // Pencegahan Duplikasi: Jika update_id ini sudah pernah diproses, tolak duplikasi
        if (!empty($update['update_id'])) {
            $cacheKey = 'telegram_update_processed_' . $update['update_id'];
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                Log::info("Telegram update {$update['update_id']} sudah pernah diproses. Melewati respon duplikat.");
                return response()->json(['status' => 'already_processed']);
            }
            \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(30));
        }

        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'] ?? null;
            $text = trim($message['text'] ?? '');
            $senderName = $message['from']['first_name'] ?? 'Pengunjung';

            if ($chatId && !empty($text)) {
                $replyText = $this->handleTelegramCommand($chatId, $text, $senderName);
                if (!empty($replyText)) {
                    $this->telegramService->sendMessage((string) $chatId, $replyText);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Webhook Verification & Incoming Message Handler untuk WhatsApp Cloud API
     * URL: GET & POST /integration/whatsapp/webhook
     */
    public function whatsappWebhook(Request $request)
    {
        // 1. Meta Webhook Verification (GET)
        if ($request->isMethod('get')) {
            $verifyToken = IntegrationSetting::get('WHATSAPP_VERIFY_TOKEN', env('WHATSAPP_VERIFY_TOKEN', 'librarycare_verify_token'));
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            return response('Forbidden', 403);
        }

        // 2. Incoming WhatsApp Message Handler (POST)
        $payload = $request->all();
        Log::info('WhatsApp Webhook Received', ['payload' => $payload]);
        if (isset($payload['entry'][0]['changes'][0]['value']['statuses'][0]['errors'])) {
            Log::error('Meta Delivery Error: ' . json_encode($payload['entry'][0]['changes'][0]['value']['statuses'][0]['errors']));
        }

        // A. Format Fonnte Gateway (Sangat populer untuk nomor WhatsApp pribadi via Scan QR)
        if ($request->has('sender') && $request->has('message')) {
            $from = trim($request->input('sender'));
            $body = trim($request->input('message'));
            $senderName = trim($request->input('name') ?: 'Pengunjung WhatsApp');

            if (!empty($from) && !empty($body)) {
                $this->handleWhatsAppMessage($from, $body, $senderName);
            }
            return response()->json(['status' => 'ok']);
        }

        // B. Format Resmi Meta WhatsApp Business Cloud API
        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            $msg = $payload['entry'][0]['changes'][0]['value']['messages'][0];
            $from = $msg['from'] ?? null; // e.g. 6288212093276
            $body = trim($msg['text']['body'] ?? '');
            $senderName = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? 'Pengunjung WhatsApp';

            if ($from && !empty($body)) {
                $this->handleWhatsAppMessage($from, $body, $senderName);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Proses Perintah & Pembuatan Laporan dari WhatsApp
     * PENTING: Hanya merespons jika diawali KODE KHUSUS (#LAPOR atau Template LibraryCare).
     * Jika chat pribadi biasa dari teman/keluarga, sistem DIAM TOTAL (tidak membalas & tidak menyimpan).
     */
    protected function handleWhatsAppMessage(string $from, string $text, string $senderName): void
    {
        // 1. Cek Status Pengaduan: status LIB-2026-0001
        if (preg_match('/^status\s+(LIB-\d{4}-\d{4})/i', $text, $matches)) {
            $ticket = strtoupper($matches[1]);
            $complaint = Complaint::where('ticket_number', $ticket)->first();

            if (!$complaint) {
                $reply = "Mohon maaf, tiket *{$ticket}* tidak ditemukan dalam database LibraryCare.";
            } else {
                $statusLabels = [
                    'pending' => 'Menunggu Peninjauan Petugas',
                    'processing' => 'Sedang Diproses oleh Petugas',
                    'resolved' => 'Selesai Ditangani',
                    'rejected' => 'Ditolak / Ditutup',
                ];
                $statusText = $statusLabels[$complaint->status] ?? ucfirst($complaint->status);

                $reply = "📋 *Status Laporan #{$complaint->ticket_number}*\n\n"
                    . "📌 Judul: {$complaint->title}\n"
                    . "📂 Kategori: {$complaint->category->name}\n"
                    . "🔄 Status: *{$statusText}*\n\n"
                    . "Keterangan: Laporan sedang ditindaklanjuti oleh petugas perpustakaan.";
            }

            $this->whatsAppService->sendMessage($from, $reply);
            return;
        }

        // 2. Pemrosesan Laporan Keluhan / Aspirasi Baru
        $title = null;
        $description = null;
        $category = null;

        // Kode A: Format #LAPOR Judul | Rincian atau #LAPOR [Kategori] | Rincian
        if (preg_match('/^(#lapor|lapor:)/i', $text)) {
            $cleaned = preg_replace('/^(#lapor|lapor:)\s*/i', '', $text);

            // Ekstrak Kategori jika ada format [Nama Kategori]
            if (preg_match('/\[([^\]]+)\]/', $cleaned, $matchCat)) {
                $catKeyword = trim($matchCat[1]);
                $category = ComplaintCategory::where('name', 'LIKE', "%{$catKeyword}%")->first();
                $cleaned = trim(str_replace($matchCat[0], '', $cleaned));
            }

            // Ekstrak Nama jika ada format "Nama: ..."
            if (preg_match('/nama\s*:\s*([^|\n\r]+)/i', $text, $matchName)) {
                $customName = trim($matchName[1]);
                if (!empty($customName) && !str_starts_with($customName, '[')) {
                    $senderName = $customName;
                }
            }

            $parts = explode('|', $cleaned);
            $rawTitle = '';
            $descParts = [];

            foreach ($parts as $part) {
                $partTrim = trim($part);
                if (empty($partTrim)) {
                    continue;
                }

                if (preg_match('/^nama\s*:/i', $partTrim)) {
                    continue;
                }

                if (preg_match('/^rincian\s*:\s*(.*)/is', $partTrim, $mDesc)) {
                    $descParts[] = trim($mDesc[1]);
                    continue;
                }

                if (empty($rawTitle)) {
                    $rawTitle = $partTrim;
                } else {
                    $descParts[] = $partTrim;
                }
            }

            $title = !empty($rawTitle) ? $rawTitle : ($category ? $category->name : 'Laporan Pengunjung via WhatsApp');
            $description = !empty($descParts) ? implode("\n", $descParts) : $title;
        }
        // Kode B: Format Template Resmi Tombol Web (Mengandung kata 'LibraryCare' dan 'Topik/Lokasi')
        elseif (stripos($text, 'LibraryCare') !== false && preg_match('/(topik|lokasi)\s*:\s*([^\n\r]+)/i', $text, $matchTopik)) {
            $title = trim($matchTopik[2]);
            if (preg_match('/(kendala|keluhan|rincian)\s*:\s*(.+)$/is', $text, $matchDesc)) {
                $description = trim($matchDesc[2]);
            } else {
                $description = $text;
            }
            if (preg_match('/nama\s*:\s*([^\n\r]+)/i', $text, $matchName)) {
                $senderName = trim($matchName[1]);
            }
            if (preg_match('/\[([^\]]+)\]/', $text, $matchCat)) {
                $catKeyword = trim($matchCat[1]);
                $category = ComplaintCategory::where('name', 'LIKE', "%{$catKeyword}%")->first();
            }
        }
        // JIKA BUKAN KODE LAPORAN (Chat pribadi teman, keluarga, dll.):
        else {
            // DIAM TOTAL (Silent Ignore). Tidak membalas dan tidak menyimpan apapun.
            return;
        }

        // Jika kategori belum terdeteksi dari kurung [], deteksi cerdas berdasarkan kata kunci
        if (!$category) {
            $lowerText = strtolower($text);
            if (preg_match('/(buku|jurnal|e-resource|pinjam|denda|sirkulasi|kembali)/i', $lowerText)) {
                $category = ComplaintCategory::where('slug', 'koleksi-buku-e-resource')->first()
                    ?? ComplaintCategory::where('slug', 'layanan-sirkulasi-denda')->first();
            } elseif (preg_match('/(wifi|wi-fi|internet|komputer|pc|jaringan|koneksi)/i', $lowerText)) {
                $category = ComplaintCategory::where('slug', 'wifi-komputer-it')->first();
            } elseif (preg_match('/(petugas|staf|pegawai|kasir|pustakawan)/i', $lowerText)) {
                $category = ComplaintCategory::where('slug', 'pelayanan-petugas')->first();
            } elseif (preg_match('/(saran|usul|masukan|ide|kritik)/i', $lowerText)) {
                $category = ComplaintCategory::where('slug', 'kritik-saran-umum')->first();
            }
        }

        // Default kategori jika tetap tidak ditemukan
        if (!$category) {
            $category = ComplaintCategory::where('slug', 'fasilitas-ruangan')->first()
                ?? ComplaintCategory::first();
        }

        $ticketNumber = Complaint::generateTicketNumber();

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'visitor_name' => $senderName,
            'visitor_email' => $from . '@whatsapp.user',
            'visitor_phone' => $from,
            'category_id' => $category->id,
            'title' => Str::limit($title, 120),
            'description' => $description,
            'status' => 'pending',
            'source' => 'whatsapp',
        ]);

        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'new_status' => 'pending',
            'notes' => 'Laporan masuk secara otomatis dari percakapan WhatsApp.',
        ]);

        // Balas nomor tiket konfirmasi ke WhatsApp pengirim
        $this->whatsAppService->sendTicketCreatedNotification($complaint);
    }

    private function handleTelegramCommand(int $chatId, string $text, string $senderName): string
    {
        // Dapatkan tiket resmi nyata dari database untuk contoh cek status
        $sampleTicket = Complaint::latest()->value('ticket_number') ?? 'LIB-2026-0001';
        $myLastTicket = Complaint::where('visitor_telegram', (string)$chatId)->latest()->value('ticket_number');
        $statusExampleTicket = $myLastTicket ?: $sampleTicket;

        if (str_starts_with($text, '/start')) {
            $parts = explode(' ', trim($text));

            // Jika /start membawa kode tiket dari link website (Deep Link: t.me/LibraryCare_bot?start=LIB-2026-0001)
            if (count($parts) > 1 && !empty($parts[1])) {
                $ticketArg = strtoupper(trim($parts[1]));
                $complaint = Complaint::where('ticket_number', $ticketArg)->first();

                if ($complaint) {
                    $complaint->visitor_telegram = (string) $chatId;
                    $complaint->save();

                    $statusLabels = [
                        'pending' => '⏳ Menunggu Peninjauan Petugas',
                        'processing' => '⚙️ Sedang Diproses Petugas',
                        'resolved' => '✅ Selesai Ditangani',
                        'rejected' => '❌ Ditolak / Tidak Diproses',
                    ];
                    $currentStatus = $statusLabels[$complaint->status] ?? ucfirst($complaint->status);

                    return "🎉 *BERHASIL TERHUBUNG DENGAN TIKET!* 🎉\n\n"
                        . "Halo *{$senderName}*,\n"
                        . "Laporan dengan Nomor Tiket *`{$complaint->ticket_number}`* telah sukses ditautkan ke akun Telegram Anda!\n\n"
                        . "📌 *Judul:* {$complaint->title}\n"
                        . "📂 *Kategori:* {$complaint->category->name}\n"
                        . "🔄 *Status Terkini:* {$currentStatus}\n\n"
                        . "✨ Anda tidak perlu login. Setiap petugas mengubah status atau memberikan komentar baru, notifikasinya akan otomatis dikirimkan langsung ke obrolan ini!";
                }
            }

            return "Halo *{$senderName}*! 👋\n\n"
                . "Selamat datang di *Bot Notifikasi & Pengaduan LibraryCare Perpustakaan Umum*.\n\n"
                . "🔑 *Chat ID Anda:* `{$chatId}`\n\n"
                . "📝 *Format Lapor via Bot:*\n"
                . "`/lapor [Judul Singkat] | [Rincian Keluhan]`\n\n"
                . "📌 *Contoh Format (Tekan untuk salin):*\n"
                . "`/lapor Fasilitas Rusak | AC lantai 2 kurang dingin tolong dicek`\n\n"
                . "🔍 *Cek Status Tiket:*\n"
                . "Ketik: `/status {$statusExampleTicket}`\n"
                . "_(Tekan teks kode tiket asli di atas untuk langsung mencoba mengecek status)_\n\n"
                . "🌐 *Portal Web:* Anda juga dapat melapor via website di https://librarycare.alwaysdata.net tanpa perlu login.";
        }

        if (str_starts_with($text, '/help') || str_starts_with($text, '/bantuan')) {
            return "📖 *PANDUAN BOT LIBRARYCARE:*\n\n"
                . "• `/lapor Judul | Deskripsi` - Kirim laporan pengaduan baru langsung dari Telegram.\n"
                . "• `/status {$statusExampleTicket}` - Melacak progres tiket pengaduan.\n"
                . "• `/start` - Mulai interaksi & mengetahui Chat ID Anda ({$chatId}).";
        }

        // Handler untuk membuat laporan baru via Telegram: /lapor Judul | Deskripsi
        if (str_starts_with($text, '/lapor')) {
            // Anti-duplikasi debouncing: jika laporan identik dikirim dalam 15 detik, abaikan duplikatnya
            $debounceKey = 'telegram_lapor_' . $chatId . '_' . md5(trim($text));
            if (\Illuminate\Support\Facades\Cache::has($debounceKey)) {
                return '';
            }
            $content = trim(substr($text, 6));

            // Jika user hanya menekan shortcut /lapor tanpa parameter judul/deskripsi
            if (empty($content)) {
                return "📝 *PANDUAN MEMBUAT LAPORAN BARU*\n\n"
                    . "Silakan kirimkan laporan Anda dengan format:\n"
                    . "`/lapor [Judul Singkat] | [Rincian Keluhan]`\n\n"
                    . "💡 *Contoh Siap Salin (Copy-Paste):*\n"
                    . "`/lapor AC Lantai 2 Kurang Dingin | Suhu ruangan di meja baca 5 terasa gerah tolong dicek`\n\n"
                    . "_(Tekan teks contoh di atas untuk menyalin ke kolom chat, lalu ubah sesuai keluhan Anda sebelum dikirim)_";
            }

            \Illuminate\Support\Facades\Cache::put($debounceKey, true, now()->addSeconds(15));

            $parts = explode('|', $content);
            $title = trim($parts[0]);
            $description = trim($parts[1] ?? (count($parts) === 1 ? $parts[0] : 'Tidak ada keterangan'));

            // Ambil akun user jika chat_id sudah ditautkan
            $user = User::where('telegram_chat_id', (string)$chatId)->first();

            $category = ComplaintCategory::firstOrCreate(
                ['slug' => 'kritik-saran-umum'],
                ['name' => 'Kritik & Saran Umum', 'icon' => 'sparkles', 'color' => 'purple', 'is_active' => true]
            );

            $ticketNumber = Complaint::generateTicketNumber();

            $complaint = Complaint::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $user?->id,
                'visitor_name' => $user?->name ?? $senderName,
                'visitor_email' => $user?->email ?? ($chatId . '@telegram.user'),
                'visitor_phone' => $user?->phone,
                'visitor_telegram' => (string)$chatId,
                'category_id' => $category->id,
                'title' => Str::limit($title, 120),
                'description' => $description,
                'status' => 'pending',
                'source' => 'telegram',
            ]);

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'new_status' => 'pending',
                'notes' => 'Laporan dibuat secara langsung via Telegram Bot oleh ' . $senderName,
            ]);

            return "✅ *PENGADUAN ANDA BERHASIL DICATAT!*\n\n"
                . "📋 *Nomor Tiket:* `{$complaint->ticket_number}`\n"
                . "📌 *Judul:* {$complaint->title}\n"
                . "📂 *Kategori:* {$complaint->category->name}\n"
                . "⏱️ *Status:* ⏳ Menunggu Peninjauan Petugas\n\n"
                . "Laporan ini telah masuk ke Dashboard Petugas Perpustakaan. Setiap pembaruan status akan langsung diinfokan ke bot ini!";
        }

        if (str_starts_with($text, '/status')) {
            $parts = explode(' ', trim($text));

            // Jika user hanya menekan shortcut /status tanpa mengetik nomor tiket
            if (count($parts) < 2 || empty($parts[1])) {
                $myTickets = Complaint::where('visitor_telegram', (string)$chatId)->latest()->take(3)->get();

                if ($myTickets->isNotEmpty()) {
                    $reply = "📋 *STATUS TIKET PENGADUAN ANDA:*\n\n";
                    foreach ($myTickets as $t) {
                        $st = match ($t->status) {
                            'pending' => '⏳ Menunggu Peninjauan Petugas',
                            'processing' => '⚙️ Sedang Diproses',
                            'resolved' => '✅ Selesai Ditangani',
                            'rejected' => '❌ Ditolak / Ditutup',
                            default => ucfirst($t->status),
                        };
                        $reply .= "• `{$t->ticket_number}`: *{$t->title}*\n  Status: {$st}\n\n";
                    }
                    $reply .= "Ketik `/status NOMOR_TIKET` untuk melihat rincian lengkap tiket tertentu.";
                    return $reply;
                }

                return "🔍 *CARA CEK STATUS PENGADUAN*\n\n"
                    . "Ketik perintah `/status` diikuti nomor tiket resmi Anda.\n\n"
                    . "📋 *Contoh Tiket Asli Sistem (Tekan untuk coba):*\n"
                    . "`/status {$sampleTicket}`\n\n"
                    . "_(Tekan teks kode di atas untuk menyalin & langsung cek tiket di sistem)_";
            }

            $ticket = strtoupper(trim($parts[1]));
            $complaint = Complaint::where('ticket_number', $ticket)->first();

            if (!$complaint) {
                return "❌ Nomor tiket `{$ticket}` tidak ditemukan dalam sistem database LibraryCare. Mohon periksa kembali.";
            }

            $statusText = match ($complaint->status) {
                'pending' => '⏳ Menunggu Peninjauan Petugas',
                'processing' => '⚙️ Sedang Diproses',
                'resolved' => '✅ Selesai Ditangani',
                'rejected' => '❌ Ditolak / Ditutup',
                default => ucfirst($complaint->status),
            };

            return "📋 *INFORMASI LAPORAN LIBRARYCARE*\n\n"
                . "• *Nomor Tiket:* `{$complaint->ticket_number}`\n"
                . "• *Judul:* {$complaint->title}\n"
                . "• *Kategori:* {$complaint->category->name}\n"
                . "• *Status:* *{$statusText}*\n"
                . "• *Tanggal Lapor:* " . $complaint->created_at->format('d M Y, H:i') . " WIB\n\n"
                . "Untuk rincian riwayat penanganan lengkap, silakan kunjungi website LibraryCare.";
        }

        return "Halo *{$senderName}*, perintah tidak dikenali.\n"
            . "• Ketik `/lapor Judul | Keluhan` untuk membuat pengaduan baru.\n"
            . "• Ketik `/status NOMOR_TIKET` untuk cek perkembangan laporan Anda.";
    }
}
