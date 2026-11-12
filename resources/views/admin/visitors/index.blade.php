@extends('layouts.admin')

@section('title', 'Data Pengunjung Perpustakaan')
@section('header_title', 'Manajemen Data Pengunjung Terdaftar')

@section('content')
<div class="space-y-6">
    <!-- Header with Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-navy-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Direktori Pengguna & Mahasiswa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau akun pengunjung yang telah mengajukan pengaduan atau mendaftar</p>
        </div>

        <form action="{{ route('admin.visitors.index') }}" method="GET" class="relative max-w-xs w-full">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, nomor..."
                class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
        </form>
    </div>

    <!-- Visitors Table -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 dark:bg-navy-950 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Pengunjung</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">WhatsApp</th>
                        <th class="py-3 px-4">Telegram Linked</th>
                        <th class="py-3 px-4">Total Laporan</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($visitors as $visitor)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 dark:bg-teal-950 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($visitor->name, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $visitor->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                {{ $visitor->email }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                {{ $visitor->phone ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($visitor->telegram_chat_id)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Aktif ({{ $visitor->telegram_chat_id }})</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                        Belum Terhubung
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white">
                                <span class="px-2 py-0.5 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300">
                                    {{ $visitor->complaints_count }} Tiket
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $visitor->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.visitors.show', $visitor->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
                                    <span>Riwayat</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                Belum ada data pengunjung terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visitors->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $visitors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

