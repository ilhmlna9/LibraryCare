<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\IntegrationSetting;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $adminChatId;
    protected bool $isSimulation;

    public function __construct()
    {
        $this->botToken = IntegrationSetting::get('TELEGRAM_BOT_TOKEN', env('TELEGRAM_BOT_TOKEN'));
        $this->adminChatId = IntegrationSetting::get('TELEGRAM_ADMIN_CHAT_ID', env('TELEGRAM_ADMIN_CHAT_ID'));

        $simSetting = IntegrationSetting::get('TELEGRAM_SIMULATION_MODE', env('TELEGRAM_SIMULATION_MODE', 'true'));
        $this->isSimulation = filter_var($simSetting, FILTER_VALIDATE_BOOLEAN) || empty($this->botToken);
    }

    public function isSimulationMode(): bool
    {
        return $this->isSimulation;
    }

    public function notifyAdminNewComplaint(Complaint $complaint): array
    {
        $targetChatId = $this->adminChatId;
        if (empty($targetChatId)) {
            $targetChatId = 'ADMIN_CHANNEL';
        }

        $sourceLabels = [
            'website' => '🌐 Website Form',
            'whatsapp' => '💬 WhatsApp',
            'telegram' => '✈️ Telegram Bot',
            'google_forms' => '📋 Google Forms',
        ];
        $sourceText = $sourceLabels[$complaint->source] ?? $complaint->source;

        $message = "🚨 *PENGADUAN PERPUSTAKAAN BARU* 🚨\n\n"
            . "📋 *Nomor Tiket:* `{$complaint->ticket_number}`\n"
            . "👤 *Pelapor:* {$complaint->visitor_name}\n"
            . "📧 *Email:* {$complaint->visitor_email}\n"
            . "📱 *WhatsApp:* " . ($complaint->visitor_phone ?? '-') . "\n"
            . "📂 *Kategori:* {$complaint->category->name}\n"
            . "📥 *Sumber Kanal:* {$sourceText}\n\n"
            . "📌 *Judul:* {$complaint->title}\n"
            . "📝 *Deskripsi:*\n_{$complaint->description}_\n\n"
            . "⏱️ *Waktu Masuk:* " . $complaint->created_at->format('d M Y, H:i') . " WIB\n\n"
            . "Silakan login ke Admin Dashboard LibraryCare untuk menindaklanjuti laporan ini.";

        return $this->sendMessage($targetChatId, $message, $complaint->id, 'markdown');
    }

    public function notifyVisitorNewComplaint(Complaint $complaint): array
    {
        $chatId = $this->resolveVisitorTelegramChatId($complaint);

        if (empty($chatId)) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Chat ID Telegram pengunjung tidak tersedia.'];
        }

        $appUrl = url('/track?ticket=' . urlencode($complaint->ticket_number));

        $message = "✨ *TERIMA KASIH ATAS LAPORAN ANDA* ✨\n\n"
            . "Halo *{$complaint->visitor_name}*,\n\n"
            . "Laporan serta saran Anda telah berhasil kami terima dan tercatat di sistem *LibraryCare* Layanan Perpustakaan.\n\n"
            . "🎫 *Kode Tiket Anda:* `{$complaint->ticket_number}`\n"
            . "📌 *Judul Laporan:* {$complaint->title}\n"
            . "📂 *Kategori:* {$complaint->category->name}\n"
            . "⏱️ *Status Awal:* Menunggu Peninjauan Petugas\n\n"
            . "Setiap pembaruan penanganan dari petugas akan kami sampaikan secara otomatis ke Telegram Anda.\n"
            . "Anda juga dapat melacak perkembangan laporan secara mandiri melalui tautan berikut:\n"
            . "🔗 [Klik di Sini untuk Melacak Tiket]({$appUrl})\n\n"
            . "Kritik dan saran Anda sangat berharga bagi peningkatan kenyamanan dan mutu fasilitas perpustakaan kita bersama.\n\n"
            . "Salam hangat,\n"
            . "🏛️ _Tim Layanan & Manajemen Perpustakaan_";

        return $this->sendMessage($chatId, $message, $complaint->id, 'markdown');
    }

    public function notifyVisitorStatusChanged(Complaint $complaint, string $oldStatus, string $newStatus, ?string $notes = null): array
    {
        $chatId = $this->resolveVisitorTelegramChatId($complaint);

        if (empty($chatId)) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Akun Telegram pengunjung belum tersedia.'];
        }

        $statusLabels = [
            'pending' => '⏳ Menunggu Peninjauan',
            'processing' => '⚙️ Sedang Diproses Petugas',
            'resolved' => '✅ Selesai Ditangani',
            'rejected' => '❌ Ditolak / Tidak Dapat Diproses',
        ];

        $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        $appUrl = url('/track?ticket=' . urlencode($complaint->ticket_number));

        $message = "🔔 *PEMBARUAN STATUS LAPORAN PERPUSTAKAAN* 🔔\n\n"
            . "Halo *{$complaint->visitor_name}*,\n\n"
            . "Petugas perpustakaan telah memperbarui status penanganan laporan Anda:\n\n"
            . "🎫 *Kode Tiket:* `{$complaint->ticket_number}`\n"
            . "📌 *Judul Laporan:* {$complaint->title}\n"
            . "🔄 *Status Terbaru:* *{$newLabel}*\n";

        if ($notes) {
            $message .= "💬 *Catatan / Tindak Lanjut Petugas:*\n_{$notes}_\n";
        }

        if ($newStatus === 'resolved') {
            $message .= "\n🎉 *Alhamdulillah, laporan Anda telah selesai ditangani dengan baik!* Terima kasih atas kesabaran dan partisipasi aktif Anda.\n"
                . "Mohon kesediaan Anda untuk memberikan ulasan dan rating kepuasan layanan kami di website:\n"
                . "⭐ [Beri Ulasan & Nilai Kepuasan]({$appUrl})\n";
        } else {
            $message .= "\n🔎 Anda dapat memantau linimasa progres penanganan selengkapnya di:\n{$appUrl}\n";
        }

        $message .= "\nSalam hormat,\n🏛️ _Tim Layanan Perpustakaan_";

        return $this->sendMessage($chatId, $message, $complaint->id, 'markdown');
    }

    public function notifyVisitorReplyAdded(Complaint $complaint, string $replyMessage): array
    {
        $chatId = $this->resolveVisitorTelegramChatId($complaint);

        if (empty($chatId)) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Chat ID Telegram tidak tersedia.'];
        }

        $appUrl = url('/track?ticket=' . urlencode($complaint->ticket_number));

        $message = "💬 *TANGGAPAN BARU PETUGAS PERPUSTAKAAN* 💬\n\n"
            . "Halo *{$complaint->visitor_name}*,\n\n"
            . "Petugas perpustakaan telah memberikan tanggapan / tindak lanjut untuk laporan Anda:\n\n"
            . "🎫 *Kode Tiket:* `{$complaint->ticket_number}`\n"
            . "📌 *Judul Laporan:* {$complaint->title}\n\n"
            . "🗣️ *Pesan Petugas:*\n"
            . "_{$replyMessage}_\n\n"
            . "🔎 Anda dapat memantau riwayat percakapan lengkap di:\n"
            . "🔗 [Lihat Status & Riwayat Tiket]({$appUrl})\n\n"
            . "Salam hormat,\n🏛️ _Tim Layanan Perpustakaan_";

        return $this->sendMessage($chatId, $message, $complaint->id, 'markdown');
    }

    /**
     * Cari Chat ID Telegram pengunjung:
     * HANYA mengambil Chat ID milik pelapor spesifik laporan ini.
     * Jika pelapor tidak menautkan Telegram, return null (TIDAK ADA broadcast ke user/admin lain).
     */
    protected function resolveVisitorTelegramChatId(Complaint $complaint): ?string
    {
        if (!empty($complaint->visitor_telegram)) {
            return trim($complaint->visitor_telegram);
        }

        if ($complaint->user && !empty($complaint->user->telegram_chat_id)) {
            return trim($complaint->user->telegram_chat_id);
        }

        return null;
    }

    public function sendMessage(string $chatId, string $text, ?int $complaintId = null, string $parseMode = 'markdown'): array
    {
        if ($this->isSimulation) {
            $log = NotificationLog::create([
                'complaint_id' => $complaintId,
                'type' => 'telegram',
                'recipient' => $chatId,
                'message' => $text,
                'status' => 'simulated',
                'response' => json_encode([
                    'mode' => 'SIMULASI_LOKAL',
                    'note' => 'Telegram Bot Token belum dikonfigurasi. Notifikasi bot disimulasikan berhasil.',
                    'target_chat_id' => $chatId,
                    'timestamp' => now()->toIso8601String(),
                ]),
            ]);

            return [
                'success' => true,
                'simulated' => true,
                'status' => 'simulated',
                'message' => 'Telegram Notification tercatat dalam Mode Simulasi.',
                'log_id' => $log->id,
            ];
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            $response = Http::withoutVerifying()->post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
            ]);

            $status = $response->successful() ? 'sent' : 'failed';
            $body = $response->json();

            NotificationLog::create([
                'complaint_id' => $complaintId,
                'type' => 'telegram',
                'recipient' => $chatId,
                'message' => $text,
                'status' => $status,
                'response' => json_encode($body),
            ]);

            return [
                'success' => $response->successful(),
                'simulated' => false,
                'status' => $status,
                'message' => $response->successful() ? 'Pesan Telegram berhasil terkirim.' : 'Gagal mengirim ke Telegram.',
                'response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('Telegram API Error: ' . $e->getMessage());

            NotificationLog::create([
                'complaint_id' => $complaintId,
                'type' => 'telegram',
                'recipient' => $chatId,
                'message' => $text,
                'status' => 'failed',
                'response' => json_encode(['error' => $e->getMessage()]),
            ]);

            return [
                'success' => false,
                'simulated' => false,
                'status' => 'failed',
                'message' => 'Error Telegram API: ' . $e->getMessage(),
            ];
        }
    }

    public function getBotInfo(): array
    {
        if (empty($this->botToken)) {
            return ['ok' => false, 'description' => 'Bot Token belum diisi di konfigurasi .env atau database.'];
        }

        try {
            $response = Http::withoutVerifying()->get("https://api.telegram.org/bot{$this->botToken}/getMe");
            return $response->json();
        } catch (\Exception $e) {
            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }
}

