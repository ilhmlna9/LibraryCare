<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\CustomerSatisfaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', 'pending')->count(),
            'processing' => Complaint::where('status', 'processing')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'rejected' => Complaint::where('status', 'rejected')->count(),
            'avg_rating' => round(CustomerSatisfaction::avg('rating') ?? 0, 1),
            'total_ratings' => CustomerSatisfaction::count(),
            'total_visitors' => User::where('role', 'visitor')->count(),
        ];

        // 1. Data Grafik: Berdasarkan Kategori
        $categoryData = ComplaintCategory::withCount('complaints')
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'count' => $cat->complaints_count,
                    'color' => $this->getCategoryHexColor($cat->color),
                ];
            });

        // 2. Data Grafik: Trend Bulanan (6 Bulan Terakhir)
        $months = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $months[] = $month->translatedFormat('F Y');
            
            $count = Complaint::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyCounts[] = $count;
        }

        // 3. Data Grafik: Distribusi Sumber Pengaduan (Website, WA, Telegram, Google Forms)
        $sourceDistribution = [
            'website' => Complaint::where('source', 'website')->count(),
            'whatsapp' => Complaint::where('source', 'whatsapp')->count(),
            'telegram' => Complaint::where('source', 'telegram')->count(),
            'google_forms' => Complaint::where('source', 'google_forms')->count(),
        ];

        // 4. Tabel Laporan Terbaru dengan filter & search
        $query = Complaint::with(['category', 'user', 'satisfaction']);

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
                  ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $latestComplaints = $query->latest()->paginate(10)->withQueryString();
        $categories = ComplaintCategory::where('is_active', true)->get();

        return view('admin.dashboard', compact(
            'stats',
            'categoryData',
            'months',
            'monthlyCounts',
            'sourceDistribution',
            'latestComplaints',
            'categories'
        ));
    }

    private function getCategoryHexColor(string $color): string
    {
        return match ($color) {
            'amber' => '#f59e0b',
            'indigo' => '#6366f1',
            'teal' => '#14b8a6',
            'sky' => '#0ea5e9',
            'rose' => '#f43f5e',
            'purple' => '#a855f7',
            'emerald' => '#10b981',
            default => '#3b82f6',
        };
    }
}

