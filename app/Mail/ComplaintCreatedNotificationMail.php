<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintCreatedNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Complaint $complaint;
    public string $trackUrl;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
        $this->trackUrl = url('/track?ticket=' . urlencode($complaint->ticket_number));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎟️ [{$this->complaint->ticket_number}] Konfirmasi Pengaduan Layanan Perpustakaan",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.complaint_created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
