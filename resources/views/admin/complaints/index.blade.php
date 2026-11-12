@extends('layouts.admin')

@section('title', 'Daftar Pengaduan & Saran Pengunjung')
@section('header_title', 'Manajemen Pengaduan & Saran Pengunjung')

@section('content')
<div class="space-y-6">
    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.complaints.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ !request('status') ? 'bg-slate-900 text-white dark:bg-teal-600' : 'bg-white dark:bg-navy-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
            Semua ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.complaints.index', ['status' => 'pending']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'pending' ? 'bg-amber-500 text-slate-950 shadow-sm' : 'bg-white dark:bg-navy-900 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900 hover:bg-amber-50/50' }}">
            Menunggu ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.complaints.index', ['status' => 'processing']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'processing' ? 'bg-sky-500 text-white shadow-sm' : 'bg-white dark:bg-navy-900 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-900 hover:bg-sky-50/50' }}">
            Diproses ({{ $counts['processing'] }})
        </a>
        <a href="{{ route('admin.complaints.index', ['status' => 'resolved']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-navy-900 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900 hover:bg-emerald-50/50' }}">
            Selesai ({{ $counts['resolved'] }})
        </a>
        <a href="{{ route('admin.complaints.index', ['status' => 'rejected']) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-navy-900 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900 hover:bg-rose-50/50' }}">
            Ditolak ({{ $counts['rejected'] }})
        </a>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.complaints.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="sm:col-span-5 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tiket, judul, nama pelapor, telepon..."
                    class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
            </div>

            <div class="sm:col-span-3">
                <select name="category_id" class="w-full py-2.5 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="source" class="w-full py-2.5 px-3 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none">
                    <option value="">Semua Kanal</option>
                    <option value="website" {{ request('source') === 'website' ? 'selected' : '' }}>Website</option>
                    <option value="whatsapp" {{ request('source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="telegram" {{ request('source') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="google_forms" {{ request('source') === 'google_forms' ? 'selected' : '' }}>Google Forms</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-3 rounded-xl bg-slate-900 dark:bg-teal-600 text-white text-xs font-semibold hover:bg-slate-800 transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['search', 'category_id', 'source', 'status']))
                    <a href="{{ route('admin.complaints.index') }}" title="Reset Filter" class="p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/70 dark:bg-navy-950 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tiket</th>
                        <th class="py-3 px-4">Pelapor</th>
                        <th class="py-3 px-4">Kategori & Judul</th>
                        <th class="py-3 px-4">Kanal</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($complaints as $complaint)
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
                            <td class="py-3.5 px-4 max-w-sm">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 mb-1">
                                    {{ $complaint->category->name }}
                                </span>
                                <p class="font-medium text-slate-800 dark:text-white truncate">{{ $complaint->title }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
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
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="p-1.5 rounded-lg text-teal-700 dark:text-teal-300 hover:bg-teal-50 dark:hover:bg-teal-950/60" title="Kelola Laporan">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket {{ $complaint->ticket_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40" title="Hapus Laporan">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                Tidak ada data pengaduan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($complaints->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

