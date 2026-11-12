<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintReply;
use App\Models\ComplaintStatusHistory;
use App\Services\EmailNotificationService;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
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

    public function index(Request $request)
    {
        $query = Complaint::with(['category', 'user', 'admin', 'satisfaction']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                  ->orWhere('visitor_name', 'LIKE', "%{$search}%")
                  ->orWhere('visitor_email', 'LIKE', "%{$search}%")
                  ->orWhere('visitor_phone', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $complaints = $query->latest()->paginate(15)->withQueryString();
        $categories = ComplaintCategory::where('is_active', true)->get();

        $counts = [
            'all' => Complaint::count(),
            'pending' => Complaint::where('status', 'pending')->count(),
            'processing' => Complaint::where('status', 'processing')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'rejected' => Complaint::where('status', 'rejected')->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'categories', 'counts'));
    }

    public function show($id)
    {
        $complaint = Complaint::with([
            'category',
            'user',
            'admin',
            'replies.user',
            'statusHistories.changer',
            'satisfaction',
            'notificationLogs',
        ])->findOrFail($id);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,resolved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $complaint->status;
        $newStatus = $validated['status'];
        $notes = $validated['notes'] ?? null;

        if ($oldStatus === $newStatus && empty($notes)) {
            return back()->with('info', 'Status laporan tidak berubah.');
        }

        $complaint->status = $newStatus;
        $complaint->admin_id = Auth::id();
        $complaint->save();

        // 1. Simpan Riwayat Perubahan Status ke Database
        ComplaintStatusHistory::create([
            'complaint_id' => $complaint->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'notes' => $notes ?: 'Status diubah menjadi ' . ucfirst($newStatus) . ' oleh ' . Auth::user()->name,
        ]);

        // 2. Notifikasi WhatsApp ke Pengunjung
        if (!empty($complaint->visitor_phone)) {
            $this->whatsAppService->sendStatusChangedNotification($complaint, $oldStatus, $newStatus, $notes);
        }

        // 3. Notifikasi Telegram Otomatis (ke bot Telegram pengadu / admin)
        $this->telegramService->notifyVisitorStatusChanged($complaint, $oldStatus, $newStatus, $notes);

        return back()->with('success', "Status laporan #{$complaint->ticket_number} berhasil diperbarui menjadi " . ucfirst($newStatus) . " dan notifikasi Telegram telah dikirim.");
    }

    public function storeReply(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string|min:3|max:3000',
            'send_whatsapp' => 'nullable|boolean',
        ], [
            'message.required' => 'Pesan tanggapan tidak boleh kosong.',
            'message.min' => 'Pesan tanggapan minimal 3 karakter.',
        ]);

        $reply = ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_admin' => true,
        ]);

        // Jika status masih pending, otomatis ubah ke processing
        if ($complaint->status === 'pending') {
            $complaint->status = 'processing';
            $complaint->admin_id = Auth::id();
            $complaint->save();

            ComplaintStatusHistory::create([
                'complaint_id' => $complaint->id,
                'old_status' => 'pending',
                'new_status' => 'processing',
                'changed_by' => Auth::id(),
                'notes' => 'Status otomatis beralih ke Diproses saat admin memberikan tanggapan.',
            ]);
        }

        // 1. Kirim notifikasi Telegram ketika admin berkomentar/menanggapi
        $this->telegramService->notifyVisitorReplyAdded($complaint, $validated['message']);

        // 2. Kirim notifikasi WhatsApp jika dicentang atau default ada nomor WA
        if ($request->boolean('send_whatsapp', true) && !empty($complaint->visitor_phone)) {
            $this->whatsAppService->sendReplyNotification($complaint, $validated['message']);
        }

        return back()->with('success', 'Tanggapan berhasil dikirim dan notifikasi Telegram telah diteruskan.');
    }

    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        $ticket = $complaint->ticket_number;

        if ($complaint->attachment && Storage::disk('public')->exists($complaint->attachment)) {
            Storage::disk('public')->delete($complaint->attachment);
        }

        $complaint->delete();

        return redirect()->route('admin.complaints.index')
            ->with('success', "Laporan {$ticket} berhasil dihapus dari sistem.");
    }
}

