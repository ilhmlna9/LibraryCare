<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintReply;
use App\Models\ComplaintStatusHistory;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class VisitorDashboardController extends Controller
{
    protected WhatsAppService $whatsAppService;
    protected TelegramService $telegramService;

    public function __construct(WhatsAppService $whatsAppService, TelegramService $telegramService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->telegramService = $telegramService;
    }

    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total' => Complaint::where('user_id', $user->id)->count(),
            'pending' => Complaint::where('user_id', $user->id)->where('status', 'pending')->count(),
            'processing' => Complaint::where('user_id', $user->id)->where('status', 'processing')->count(),
            'resolved' => Complaint::where('user_id', $user->id)->where('status', 'resolved')->count(),
        ];

        $recentComplaints = Complaint::with(['category', 'satisfaction'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $categories = ComplaintCategory::where('is_active', true)->get();

        return view('visitor.dashboard', compact('user', 'stats', 'recentComplaints', 'categories'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Complaint::with(['category', 'satisfaction'])
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();
        $categories = ComplaintCategory::where('is_active', true)->get();

        return view('visitor.history', compact('complaints', 'categories'));
    }

    public function show($ticketNumber)
    {
        $user = Auth::user();
        $complaint = Complaint::with(['category', 'replies.user', 'statusHistories.changer', 'satisfaction'])
            ->where('ticket_number', $ticketNumber)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('visitor_email', $user->email);
            })
            ->firstOrFail();

        return view('visitor.show', compact('complaint'));
    }

    public function reply(Request $request, $ticketNumber)
    {
        $user = Auth::user();
        $complaint = Complaint::where('ticket_number', $ticketNumber)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('visitor_email', $user->email);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string|min:3|max:2000',
        ], [
            'message.required' => 'Pesan balasan tidak boleh kosong.',
            'message.min' => 'Pesan balasan terlalu pendek.',
        ]);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_admin' => false,
        ]);

        return back()->with('success', 'Balasan Anda berhasil dikirim ke petugas perpustakaan.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('visitor.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'telegram_chat_id' => 'nullable|string|max:50',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->telegram_chat_id = $validated['telegram_chat_id'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil dan pengaturan kontak berhasil diperbarui!');
    }
}

