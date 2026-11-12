@extends('layouts.visitor')

@section('title', 'Dashboard Pengunjung')

@section('content')
<div class="space-y-8">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-teal-900 rounded-3xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="max-w-2xl space-y-2 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                Selamat Datang, {{ $user->name }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Portal Pelayanan & Pengaduan Anda</h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                Pantau proses penanganan kritik, saran, dan kendala fasilitas perpustakaan yang telah Anda sampaikan. Setiap respon dari pustakawan dapat Anda baca dan diskusikan di sini.
            </p>
            <div class="pt-3 flex flex-wrap gap-3">
                <a href="{{ route('landing') }}#form-pengaduan" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs bg-teal-500 text-slate-950 hover:bg-teal-400 transition-colors shadow-sm">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Kirim Pengaduan Baru</span>
                </a>
                <a href="{{ route('visitor.history') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-xs bg-white/10 hover:bg-white/20 text-white transition-colors border border-white/20">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>Riwayat Laporan Saya</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Laporan</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</h3>
            <span class="text-[11px] text-slate-400 mt-1 block">Laporan yang pernah dikirim</span>
        </div>

        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-500 mt-1">{{ $stats['pending'] }}</h3>
            <span class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 block">Belum diproses staf</span>
        </div>

        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Diproses</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-sky-500 mt-1">{{ $stats['processing'] }}</h3>
            <span class="text-[11px] text-sky-600 dark:text-sky-400 mt-1 block">Dalam perbaikan tim</span>
        </div>

        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Ditangani</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-500 mt-1">{{ $stats['resolved'] }}</h3>
            <span class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1 block">Tuntas ditindaklanjuti</span>
        </div>
    </div>

    <!-- Recent Complaints & Telegram Alert Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Recent Complaints List (Col 8) -->
        <div class="lg:col-span-8 bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Laporan Pengaduan Terakhir</h3>
                    <p class="text-xs text-slate-500">5 keluhan atau usulan terbaru yang Anda kirimkan</p>
                </div>
                <a href="{{ route('visitor.history') }}" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentComplaints as $complaint)
                    @php $badge = $complaint->status_badge; @endphp
                    <div class="p-4 sm:p-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-teal-600 dark:text-teal-400">{{ $complaint->ticket_number }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $complaint->category->name }}
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $complaint->title }}</h4>
                            <p class="text-[11px] text-slate-400">Diajukan pada {{ $complaint->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $badge['bg'] }}">
                                <i data-lucide="{{ $badge['icon'] }}" class="w-3.5 h-3.5"></i>
                                <span>{{ $badge['label'] }}</span>
                            </span>

                            <a href="{{ route('visitor.complaints.show', $complaint->ticket_number) }}" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        Anda belum pernah mengirimkan pengaduan.
                        <div class="mt-3">
                            <a href="{{ route('landing') }}#form-pengaduan" class="text-teal-600 dark:text-teal-400 font-semibold hover:underline">
                                Kirim pengaduan pertama Anda &rarr;
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Telegram Link Reminder & Contact Card (Col 4) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Telegram Banner -->
            <div class="bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-navy-900 dark:to-navy-950 rounded-2xl p-6 border border-sky-200 dark:border-sky-900/60 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center shadow-md">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Notifikasi Otomatis Telegram</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Dapatkan peringatan seketika di Telegram Anda saat petugas merespon laporan Anda.
                </p>

                @if($user->telegram_chat_id)
                    <div class="p-2.5 rounded-xl bg-white dark:bg-navy-900 border border-sky-200 dark:border-sky-800 text-xs">
                        <span class="text-slate-500">Chat ID Terhubung:</span>
                        <p class="font-mono font-bold text-sky-600 dark:text-sky-400">{{ $user->telegram_chat_id }}</p>
                    </div>
                @else
                    <a href="{{ route('visitor.profile') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline">
                        <span>Hubungkan Telegram Sekarang &rarr;</span>
                    </a>
                @endif
            </div>

            <!-- Library Help Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-4 h-4 text-teal-500"></i>
                    <span>Butuh Bantuan Cepat?</span>
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Jika ada kendala mendesak saat berada di perpustakaan, Anda dapat langsung mengunjungi Meja Layanan Sirkulasi di Lantai 1 atau menghubungi nomor WhatsApp staf jaga.
                </p>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500">
                    Senin - Jumat: 08.00 - 20.00 WIB
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

