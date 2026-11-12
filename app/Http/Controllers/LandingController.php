<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatusHistory;
use App\Models\CustomerSatisfaction;
use App\Models\IntegrationSetting;
use App\Services\EmailNotificationService;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LandingController extends Controller
{
    protected WhatsAppService $whatsAppService;
    protected TelegramService $telegramService;
    protected EmailNotificationService $emailService;

    public function __construct(
        WhatsAppService $whatsAppService,
        TelegramService $telegramService,
        EmailNotificationService $emailService
    ) {
        $this->whatsAppService = $whatsAppService;
        $this->telegramService = $telegramService;
        $this->emailService = $emailService;
    }

    public function index()
    {
        $categories = ComplaintCategory::where('is_active', true)->get();
        
        $stats = [
            'total' => Complaint::count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'avg_satisfaction' => round(CustomerSatisfaction::avg('rating') ?? 4.8, 1),
            'total_users' => \App\Models\User::where('role', 'visitor')->count(),
        ];

        $settings = [
            'library_name' => IntegrationSetting::get('LIBRARY_NAME', 'Perpustakaan Utama Kampus'),
            'library_email' => IntegrationSetting::get('LIBRARY_EMAIL', 'library@university.ac.id'),
            'library_phone' => IntegrationSetting::get('LIBRARY_PHONE', '+62 21 7890 1234'),
            'library_whatsapp' => IntegrationSetting::get('LIBRARY_WHATSAPP', '088212093276'),
            'library_telegram' => IntegrationSetting::get('LIBRARY_TELEGRAM_BOT', '@LibraryCare_bot'),
            'google_forms_url' => IntegrationSetting::get('GOOGLE_FORMS_URL', 'https://docs.google.com/forms/d/e/1FAIpQLSdEnYHc7Nc5HIEGzTOdJOkVMlbV3bS7DkHlXOvudnl8GKg4wQ/viewform'),
            'google_forms_api_key' => IntegrationSetting::get('GOOGLE_FORMS_API_KEY', 'lc_gform_paIVqyulDQlJbnyyeb88kE7F'),
            'library_address' => IntegrationSetting::get('LIBRARY_ADDRESS', 'Gedung Rektorat Lt. 2-3, Kampus Terpadu, Jl. Pendidikan No. 45'),
            'library_hours' => IntegrationSetting::get('LIBRARY_OPERATING_HOURS', 'Senin - Jumat: 08.00 - 20.00 WIB | Sabtu: 08.30 - 15.00 WIB'),
        ];

        return view('landing.index', compact('categories', 'stats', 'settings'));
    }

    public function storeComplaint(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'nullable|string|max:25',
            'visitor_telegram' => 'nullable|string|max:100',
            'category_id' => 'required|exists:complaint_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:15',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'visitor_name.required' => 'Nama lengkap wajib diisi.',
            'visitor_email.email' => 'Format email tidak valid.',
            'category_id.required' => 'Pilih kategori keluhan.',
            'title.required' => 'Judul laporan wajib diisi.',
            'description.required' => 'Deskripsi keluhan wajib diisi.',
            'description.min' => 'Deskripsi keluhan minimal 15 karakter agar jelas.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
            'attachment.mimes' => 'Format berkas harus berupa JPG, PNG, atau PDF.',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $attachmentPath = $file->storeAs('complaints', $filename, 'public');
        }

        $ticketNumber = Complaint::generateTicketNumber();

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'user_id' => null, // Guest / tamu tanpa perlu login
            'visitor_name' => $validated['visitor_name'],
            'visitor_email' => $validated['visitor_email'] ?? null,
            'visitor_phone' => $validated['visitor_phone'] ?? null,
            'visitor_telegram' => $validated['visitor_telegram'] ?? null,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
            'source' => 'website',
        ]);

        // Catat riwayat status awal
        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'old_status' => null,
            'new_status' => 'pending',
            'changed_by' => null,
            'notes' => 'Laporan berhasil dibuat melalui formulir website.',
        ]);

        // Murni masuk ke database sistem CRM admin (tanpa notifikasi eksternal saat kirim form web)
        // Nomor tiket langsung ditampilkan di layar pelacakan pengunjung (/track)

        return redirect()->route('complaints.track', ['ticket' => $ticketNumber])
            ->with('success', "Laporan Anda berhasil dikirim! Simpan Nomor Tiket Anda: {$ticketNumber}");
    }

    public function track(Request $request)
    {
        $ticket = trim($request->get('ticket', ''));
        $complaint = null;

        if (!empty($ticket)) {
            $complaint = Complaint::with(['category', 'replies.user', 'statusHistories.changer', 'satisfaction'])
                ->where('ticket_number', $ticket)
                ->first();
        }

        return view('landing.track', compact('complaint', 'ticket'));
    }

    public function submitSatisfaction(Request $request, $ticketNumber)
    {
        $complaint = Complaint::where('ticket_number', $ticketNumber)->firstOrFail();

        if ($complaint->status !== 'resolved') {
            return back()->with('error', 'Penilaian hanya dapat diberikan setelah laporan dinyatakan Selesai.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        CustomerSatisfaction::updateOrCreate(
            ['complaint_id' => $complaint->id],
            [
                'user_id' => Auth::id(),
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('success', 'Terima kasih atas penilaian dan ulasan kepuasan Anda!');
    }

    /**
     * Tampilan Interaktif Google Forms Bawaan / Simulator
     */
    public function showGoogleForm()
    {
        $categories = ComplaintCategory::where('is_active', true)->get();
        $apiKey = IntegrationSetting::get('GOOGLE_FORMS_API_KEY', 'lc_gform_demo_key');
        $webhookUrl = route('integration.google-forms.webhook');

        return view('landing.google-forms', compact('categories', 'apiKey', 'webhookUrl'));
    }
}

