<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\IntegrationSetting;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $gatewayProvider; // 'fonnte' atau 'cloud_api'
    protected string $apiUrl;
    protected ?string $phoneNumberId;
    protected ?string $accessToken;
    protected ?string $fonnteToken;
    protected bool $isSimulation;

    public function __construct()
    {
        $this->gatewayProvider = IntegrationSetting::get('WHATSAPP_GATEWAY_PROVIDER', env('WHATSAPP_GATEWAY_PROVIDER', 'fonnte'));
        $this->apiUrl = IntegrationSetting::get('WHATSAPP_API_URL', env('WHATSAPP_API_URL', 'https://graph.facebook.com/v19.0'));
        $this->phoneNumberId = IntegrationSetting::get('WHATSAPP_PHONE_NUMBER_ID', env('WHATSAPP_PHONE_NUMBER_ID'));
        $this->accessToken = IntegrationSetting::get('WHATSAPP_ACCESS_TOKEN', env('WHATSAPP_ACCESS_TOKEN'));
        $this->fonnteToken = IntegrationSetting::get('FONNTE_TOKEN', env('FONNTE_TOKEN'));

        $simSetting = IntegrationSetting::get('WHATSAPP_SIMULATION_MODE', env('WHATSAPP_SIMULATION_MODE', 'true'));
        
        $hasCredentials = ($this->gatewayProvider === 'fonnte' && !empty($this->fonnteToken)) ||
                           ($this->gatewayProvider === 'cloud_api' && !empty($this->accessToken) && !empty($this->phoneNumberId));

        $this->isSimulation = filter_var($simSetting, FILTER_VALIDATE_BOOLEAN) || !$hasCredentials;
    }

    public function isSimulationMode(): bool
    {
        return $this->isSimulation;
    }

    public function sendTicketCreatedNotification(Complaint $complaint): array
    {
        if (empty($complaint->visitor_phone)) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Nomor WhatsApp pengunjung tidak tersedia'];
        }

        $trackUrl = 'http://localhost:8000/track?ticket=' . urlencode($complaint->ticket_number);

        // Format pesan yang ramah untuk WhatsApp
        $message = "Halo *{$complaint->visitor_name}*,\n\n"
            . "Terima kasih telah menyampaikan aspirasi Anda. Laporan keluhan/saran Anda telah berhasil kami terima dan tercatat di sistem *LibraryCare* Layanan Perpustakaan.\n\n"
            . "🎫 *Kode Tiket Anda:* `{$complaint->ticket_number}`\n"
            . "📌 *Judul Laporan:* {$complaint->title}\n"
            . "📂 *Kategori:* {$complaint->category->name}\n"
            . "⏱️ *Status:* ⏳ Menunggu Peninjauan Petugas\n\n"
            . "Anda dapat memantau linimasa progres penanganan laporan di website perpustakaan:\n"
            . "👉 {$trackUrl}\n\n"
            . "Atau cukup balas pesan ini dengan ketik: *STATUS {$complaint->ticket_number}*\n\n"
            . "Terima kasih atas partisipasi Anda dalam meningkatkan kualitas fasilitas dan kenyamanan perpustakaan.\n\n"
            . "Salam hangat,\n"
            . "_Tim Layanan & Manajemen Perpustakaan_";

        return $this->sendMessage($complaint->visitor_phone, $message, $complaint->id);
    }

    public function sendStatusChangedNotification(Complaint $complaint, string $oldStatus, string $newStatus, ?string $notes = null): array
    {
        if (empty($complaint->visitor_phone)) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Nomor WhatsApp pengunjung tidak tersedia'];
        }

        $statusLabels = [
            'pending' => 'Menunggu Peninjauan Petugas',
            'processing' => 'Sedang Diproses oleh Petugas',
            'resolved' => 'Selesai Ditangani',
            'rejected' => 'Ditolak / Ditutup',
        ];

        $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        $trackUrl = url('/track?ticket=' . urlencode($complaint->ticket_number));

        $message = "Halo *{$complaint->visitor_name}*,\n\n"
            . "Terdapat pembaruan status terkini untuk tiket pengaduan perpustakaan Anda:\n\n"
            . "🎫 *Kode Tiket:* `{$complaint->ticket_number}`\n"
            . "📌 *Judul:* {$complaint->title}\n"
            . "🔄 *Status Terbaru:* *{$newLabel}*\n";

        if ($notes) {
            $message .= "💬 *Catatan Petugas:* {$notes}\n";
        }

        if ($newStatus === 'resolved') {
            $message .= "\n🎉 *Laporan Anda telah selesai ditangani dengan baik.* Terima kasih banyak atas kesabaran dan kerja sama Anda.\n\n"
                . "Mohon kesediaan Anda untuk memberikan ulasan serta rating kepuasan layanan di link berikut:\n"
                . "⭐ {$trackUrl}\n";
        } else {
            $message .= "\nAnda dapat melihat detail progres laporan kapan saja di:\n{$trackUrl}\n";
        }

        $message .= "\nSalam hangat,\n_Tim Layanan & Manajemen Perpustakaan_";

        return $this->sendMessage($complaint->visitor_phone, $message, $complaint->id);
    }

    public function sendReplyNotification(Complaint $complaint, string $replyMessage): array
    {
        if (empty($complaint->visitor_phone)) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Nomor WhatsApp pengunjung tidak tersedia'];
        }

        $message = "Halo *{$complaint->visitor_name}*,\n\n"
            . "Petugas perpustakaan telah memberikan tanggapan baru pada tiket Anda:\n\n"
            . "📋 *Nomor Tiket:* `{$complaint->ticket_number}`\n"
            . "📌 *Judul:* {$complaint->title}\n"
            . "💬 *Tanggapan Petugas:*\n\"{$replyMessage}\"\n\n"
            . "Silakan cek detail tanggapan selengkapnya di portal LibraryCare.\n\n_Tim Layanan Perpustakaan_";

        return $this->sendMessage($complaint->visitor_phone, $message, $complaint->id);
    }

    public function sendMessage(string $recipientPhone, string $message, ?int $complaintId = null): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $recipientPhone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        // Mode Simulasi
        if ($this->isSimulation) {
            $log = NotificationLog::create([
                'complaint_id' => $complaintId,
                'type' => 'whatsapp',
                'recipient' => $recipientPhone,
                'message' => $message,
                'status' => 'simulated',
                'response' => json_encode([
                    'mode' => 'SIMULASI_LOKAL',
                    'gateway' => $this->gatewayProvider,
                    'note' => 'WhatsApp Gateway Token belum diisi atau Mode Simulasi aktif. Pesan dicatat lokal.',
                    'recipient_normalized' => $cleanPhone,
                    'timestamp' => now()->toIso8601String(),
                ]),
            ]);

            return [
                'success' => true,
                'simulated' => true,
                'status' => 'simulated',
                'message' => 'Pesan tercatat dalam Mode Simulasi (Local Testing).',
                'log_id' => $log->id,
            ];
        }

        // 1. Jalur Fonnte (Scan QR WhatsApp Biasa Tanpa Meta Business)
        if ($this->gatewayProvider === 'fonnte') {
            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'Authorization' => $this->fonnteToken,
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target' => $cleanPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

                $body = $response->json();
                $isOk = $response->successful() && !empty($body['status']) && $body['status'] == true;
                $status = $isOk ? 'sent' : 'failed';

                NotificationLog::create([
                    'complaint_id' => $complaintId,
                    'type' => 'whatsapp',
                    'recipient' => $recipientPhone,
                    'message' => $message,
                    'status' => $status,
                    'response' => json_encode($body),
                ]);

                return [
                    'success' => $isOk,
                    'simulated' => false,
                    'status' => $status,
                    'message' => $isOk ? 'WhatsApp terkirim via Fonnte Gateway.' : ($body['reason'] ?? 'Gagal kirim via Fonnte'),
                    'response' => $body,
                ];
            } catch (\Exception $e) {
                Log::error('Fonnte WhatsApp Error: ' . $e->getMessage());
                NotificationLog::create([
                    'complaint_id' => $complaintId,
                    'type' => 'whatsapp',
                    'recipient' => $recipientPhone,
                    'message' => $message,
                    'status' => 'failed',
                    'response' => json_encode(['error' => $e->getMessage()]),
                ]);

                return [
                    'success' => false,
                    'simulated' => false,
                    'status' => 'failed',
                    'message' => 'Error Fonnte Gateway: ' . $e->getMessage(),
                ];
            }
        }

        // 2. Jalur WhatsApp Cloud API (Meta)
        try {
            $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";
            $response = Http::withoutVerifying()->withToken($this->accessToken)->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $cleanPhone,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message
                ],
            ]);

            $status = $response->successful() ? 'sent' : 'failed';
            $body = $response->json();

            NotificationLog::create([
                'complaint_id' => $complaintId,
                'type' => 'whatsapp',
                'recipient' => $recipientPhone,
                'message' => $message,
                'status' => $status,
                'response' => json_encode($body),
            ]);

            return [
                'success' => $response->successful(),
                'simulated' => false,
                'status' => $status,
                'message' => $response->successful() ? 'WhatsApp terkirim via Cloud API.' : 'Gagal mengirim pesan WhatsApp.',
                'response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());

            NotificationLog::create([
                'complaint_id' => $complaintId,
                'type' => 'whatsapp',
                'recipient' => $recipientPhone,
                'message' => $message,
                'status' => 'failed',
                'response' => json_encode(['error' => $e->getMessage()]),
            ]);

            return [
                'success' => false,
                'simulated' => false,
                'status' => 'failed',
                'message' => 'Error koneksi API WhatsApp: ' . $e->getMessage(),
            ];
        }
    }
}

