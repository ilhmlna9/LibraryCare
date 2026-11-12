<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Complaint $complaint;
    public string $oldStatus;
    public string $newStatus;
    public ?string $notes;
    public string $trackUrl;

    public function __construct(Complaint $complaint, string $oldStatus, string $newStatus, ?string $notes = null)
    {
        $this->complaint = $complaint;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->notes = $notes;
        $this->trackUrl = url('/track?ticket=' . urlencode($complaint->ticket_number));
    }

    public function envelope(): Envelope
    {
        $statusLabels = [
            'pending' => 'Menunggu',
            'processing' => 'Sedang Diproses',
            'resolved' => 'Selesai Ditangani',
            'rejected' => 'Ditolak',
        ];
        $label = $statusLabels[$this->newStatus] ?? ucfirst($this->newStatus);

        return new Envelope(
            subject: "🔔 [{$this->complaint->ticket_number}] Pembaruan Status: {$label}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.complaint_status_updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
