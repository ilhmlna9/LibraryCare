@extends('layouts.admin')

@section('title', 'Admin Dashboard Perpustakaan')
@section('header_title', 'Ringkasan Eksekutif & Statistik CRM')

@section('content')
<div class="space-y-6">
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Laporan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</h3>
                <span class="text-[11px] text-teal-600 dark:text-teal-400 font-medium mt-1 inline-flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3 h-3"></i> Seluruh kanal terdata
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
                <i data-lucide="inbox" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Menunggu -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Respon</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-500 mt-1">{{ $stats['pending'] }}</h3>
                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium mt-1 inline-flex items-center gap-1">
                    <i data-lucide="clock" class="w-3 h-3"></i> Perlu verifikasi admin
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <i data-lucide="hourglass" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Diproses -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Diproses</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-sky-500 mt-1">{{ $stats['processing'] }}</h3>
                <span class="text-[11px] text-sky-600 dark:text-sky-400 font-medium mt-1 inline-flex items-center gap-1">
                    <i data-lucide="refresh-cw" class="w-3 h-3"></i> Tindak lanjut aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <i data-lucide="wrench" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Ditangani</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-500 mt-1">{{ $stats['resolved'] }}</h3>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1 inline-flex items-center gap-1">
                    <i data-lucide="star" class="w-3 h-3 fill-emerald-500 text-emerald-500"></i> Rating: {{ $stats['avg_rating'] }}/5.0
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <i data-lucide="check-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section: 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Monthly Trend Line/Bar Chart (Col 7) -->
        <div class="lg:col-span-7 bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tren Pengaduan Bulanan</h3>
                    <p class="text-xs text-slate-500">Statistik intensitas keluhan masuk 6 bulan terakhir</p>
                </div>
                <span class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </span>
            </div>
            <div class="relative h-64 sm:h-72">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Categories Doughnut Chart (Col 5) -->
        <div class="lg:col-span-5 bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Laporan Berdasarkan Kategori</h3>
                    <p class="text-xs text-slate-500">Distribusi bidang keluhan pengunjung</p>
                </div>
                <span class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400">
                    <i data-lucide="pie-chart" class="w-5 h-5"></i>
                </span>
            </div>
            <div class="relative h-56 sm:h-64 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                @foreach($categoryData as $cat)
                    <div class="flex items-center gap-1.5 truncate">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat['color'] }}"></span>
                        <span class="text-slate-600 dark:text-slate-400 truncate">{{ $cat['name'] }}:</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $cat['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Multi-Channel Distribution Bar -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Kanal Masuk Laporan (Integrasi CRM)</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Website Form</span>
                    <i data-lucide="globe" class="w-4 h-4 text-indigo-500"></i>
                </div>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-2">{{ $sourceDistribution['website'] }}</p>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $stats['total'] ? ($sourceDistribution['website'] / $stats['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">WhatsApp Cloud</span>
                    <i data-lucide="message-circle" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-2">{{ $sourceDistribution['whatsapp'] }}</p>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $stats['total'] ? ($sourceDistribution['whatsapp'] / $stats['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Telegram Bot</span>
                    <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                </div>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-2">{{ $sourceDistribution['telegram'] }}</p>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-sky-500 h-full rounded-full" style="width: {{ $stats['total'] ? ($sourceDistribution['telegram'] / $stats['total'] * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Google Forms</span>
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-purple-500"></i>
                </div>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-2">{{ $sourceDistribution['google_forms'] }}</p>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-purple-500 h-full rounded-full" style="width: {{ $stats['total'] ? ($sourceDistribution['google_forms'] / $stats['total'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Complaints Table with Search & Filter -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Laporan Pengaduan Terbaru</h3>
                <p class="text-xs text-slate-500">Kelola dan ubah status laporan pengunjung</p>
            </div>
            <a href="{{ route('admin.complaints.index') }}" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1">
                <span>Buka Seluruh Laporan</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="p-4 bg-slate-50/70 dark:bg-navy-950/70 border-b border-slate-200 dark:border-slate-800">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <!-- Search -->
                <div class="relative sm:col-span-2">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tiket, nama, judul..."
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-900 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-900 text-slate-900 dark:text-white outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="category_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-900 text-slate-900 dark:text-white outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Table Responsive -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 dark:bg-navy-950 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tiket</th>
                        <th class="py-3 px-4">Pengunjung</th>
                        <th class="py-3 px-4">Kategori & Judul</th>
                        <th class="py-3 px-4">Sumber</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($latestComplaints as $complaint)
                        @php 
                            $badge = $complaint->status_badge;
                            $src = $complaint->source_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="text-teal-600 dark:text-teal-400 hover:underline">
                                    {{ $complaint->ticket_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-semibold text-slate-800 dark:text-white truncate max-w-[140px]">{{ $complaint->visitor_name }}</p>
                                <p class="text-[11px] text-slate-500 truncate max-w-[140px]">{{ $complaint->visitor_phone ?? $complaint->visitor_email }}</p>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 mb-1">
                                    {{ $complaint->category->name }}
                                </span>
                                <p class="font-medium text-slate-800 dark:text-white truncate">{{ $complaint->title }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    <i data-lucide="{{ $src['icon'] }}" class="w-3 h-3 text-teal-500"></i>
                                    <span>{{ $src['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badge['bg'] }}">
                                    <i data-lucide="{{ $badge['icon'] }}" class="w-3 h-3"></i>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                {{ $complaint->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 hover:bg-teal-100 dark:hover:bg-teal-900 border border-teal-200 dark:border-teal-800">
                                    <span>Kelola</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                Belum ada pengaduan yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($latestComplaints->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $latestComplaints->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Chart.js Monthly Trend
    const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Jumlah Pengaduan',
                data: {!! json_encode($monthlyCounts) !!},
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13, 148, 136, 0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#0d9488',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { family: 'Plus Jakarta Sans', size: 11 } },
                    grid: { color: 'rgba(148, 163, 184, 0.1)' }
                },
                x: {
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Chart.js Categories Distribution
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryData->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryData->pluck('count')) !!},
                backgroundColor: {!! json_encode($categoryData->pluck('color')) !!},
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '68%'
        }
    });
</script>
@endpush

