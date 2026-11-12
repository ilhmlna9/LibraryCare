<?php

namespace App\Services;

use App\Mail\ComplaintCreatedNotificationMail;
use App\Mail\ComplaintStatusUpdatedMail;
use App\Models\Complaint;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Kirim email otomatis terima kasih dan kode tiket ke pengunjung
     */
    public function sendTicketCreatedEmail(Complaint $complaint): array
    {
        if (empty($complaint->visitor_email) || str_ends_with($complaint->visitor_email, '@telegram.user') || str_ends_with($complaint->visitor_email, '@whatsapp.user')) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Alamat email pengunjung tidak valid atau berasal dari bot messenger.'];
        }

        try {
            Mail::to($complaint->visitor_email)->send(new ComplaintCreatedNotificationMail($complaint));

            NotificationLog::create([
                'complaint_id' => $complaint->id,
                'type' => 'email',
                'recipient' => $complaint->visitor_email,
                'message' => "Email konfirmasi pembuatan tiket #{$complaint->ticket_number} berhasil dikirim.",
                'status' => 'sent',
                'response' => json_encode(['to' => $complaint->visitor_email, 'sent_at' => now()->toIso8601String()]),
            ]);

            return [
                'success' => true,
                'status' => 'sent',
                'message' => 'Email konfirmasi nomor tiket berhasil dikirim ke pengunjung.',
            ];
        } catch (\Exception $e) {
            Log::error('Email notification failed: ' . $e->getMessage());

            NotificationLog::create([
                'complaint_id' => $complaint->id,
                'type' => 'email',
                'recipient' => $complaint->visitor_email,
                'message' => "Gagal mengirim email konfirmasi tiket #{$complaint->ticket_number}.",
                'status' => 'failed',
                'response' => json_encode(['error' => $e->getMessage()]),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim email otomatis pembaruan status pengaduan
     */
    public function sendStatusUpdatedEmail(Complaint $complaint, string $oldStatus, string $newStatus, ?string $notes = null): array
    {
        if (empty($complaint->visitor_email) || str_ends_with($complaint->visitor_email, '@telegram.user') || str_ends_with($complaint->visitor_email, '@whatsapp.user')) {
            return ['success' => false, 'status' => 'skipped', 'message' => 'Alamat email pengunjung tidak valid.'];
        }

        try {
            Mail::to($complaint->visitor_email)->send(new ComplaintStatusUpdatedMail($complaint, $oldStatus, $newStatus, $notes));

            NotificationLog::create([
                'complaint_id' => $complaint->id,
                'type' => 'email',
                'recipient' => $complaint->visitor_email,
                'message' => "Email pembaruan status #{$complaint->ticket_number} ({$newStatus}) berhasil dikirim.",
                'status' => 'sent',
                'response' => json_encode(['to' => $complaint->visitor_email, 'new_status' => $newStatus, 'sent_at' => now()->toIso8601String()]),
            ]);

            return [
                'success' => true,
                'status' => 'sent',
                'message' => 'Email pembaruan status berhasil dikirim ke pengunjung.',
            ];
        } catch (\Exception $e) {
            Log::error('Email status update failed: ' . $e->getMessage());

            NotificationLog::create([
                'complaint_id' => $complaint->id,
                'type' => 'email',
                'recipient' => $complaint->visitor_email,
                'message' => "Gagal mengirim email update status #{$complaint->ticket_number}.",
                'status' => 'failed',
                'response' => json_encode(['error' => $e->getMessage()]),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ];
        }
    }
}

