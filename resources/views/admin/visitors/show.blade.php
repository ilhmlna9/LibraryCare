@extends('layouts.admin')

@section('title', 'Profil Pengunjung: ' . $visitor->name)
@section('header_title', 'Profil Pengunjung: ' . $visitor->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.visitors.index') }}" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Riwayat & Aktivitas Pengunjung</h2>
    </div>

    <!-- Visitor Summary Card -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                    {{ strtoupper(substr($visitor->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $visitor->name }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Terdaftar sejak {{ $visitor->created_at->format('d M Y') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200 dark:border-slate-800">
                    <span class="text-slate-500">Email:</span>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $visitor->email }}</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200 dark:border-slate-800">
                    <span class="text-slate-500">WhatsApp:</span>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $visitor->phone ?? '-' }}</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200 dark:border-slate-800">
                    <span class="text-slate-500">Telegram Chat ID:</span>
                    <p class="font-bold text-slate-800 dark:text-white">{{ $visitor->telegram_chat_id ?? 'Belum Ditautkan' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History of Complaints Table -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Pengaduan yang Diajukan ({{ $visitor->complaints->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 dark:bg-navy-950 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tiket</th>
                        <th class="py-3 px-4">Kategori & Judul</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Rating</th>
                        <th class="py-3 px-4">Tanggal Masuk</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($visitor->complaints as $complaint)
                        @php $badge = $complaint->status_badge; @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-3 px-4 font-mono font-bold text-teal-600 dark:text-teal-400">
                                {{ $complaint->ticket_number }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-[10px] text-slate-500 block">{{ $complaint->category->name }}</span>
                                <span class="font-semibold text-slate-800 dark:text-white">{{ $complaint->title }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badge['bg'] }}">
                                    <i data-lucide="{{ $badge['icon'] }}" class="w-3 h-3"></i>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($complaint->satisfaction)
                                    <span class="inline-flex items-center gap-1 font-bold text-amber-500">
                                        {{ $complaint->satisfaction->rating }} <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400"></i>
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $complaint->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                    <span>Kelola</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">
                                Pengunjung ini belum pernah mengajukan pengaduan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

