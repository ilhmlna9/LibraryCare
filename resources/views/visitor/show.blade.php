@extends('layouts.visitor')

@section('title', 'Detail Tiket ' . $complaint->ticket_number)

@section('content')
<div class="space-y-6">
    <!-- Top Nav Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('visitor.history') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Riwayat Laporan</span>
        </a>
        <span class="text-xs text-slate-500 font-mono">Tiket: {{ $complaint->ticket_number }}</span>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Details, Thread & Satisfaction (Col 8) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Complaint Detail Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 mb-2">
                            {{ $complaint->category->name }}
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white leading-snug">{{ $complaint->title }}</h2>
                        <p class="text-xs text-slate-400 mt-1">Diajukan pada {{ $complaint->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>

                    @php $badge = $complaint->status_badge; @endphp
                    <div class="shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold {{ $badge['bg'] }}">
                            <i data-lucide="{{ $badge['icon'] }}" class="w-3.5 h-3.5"></i>
                            <span>{{ $badge['label'] }}</span>
                        </span>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Deskripsi Pengaduan</h4>
                    <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-navy-950 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800 whitespace-pre-line">
                        {{ $complaint->description }}
                    </div>
                </div>

                @if($complaint->attachment)
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Lampiran Berkas</h4>
                        @php $ext = pathinfo($complaint->attachment, PATHINFO_EXTENSION); @endphp
                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                            <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 max-w-sm">
                                <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $complaint->attachment) }}" alt="Lampiran" class="w-full h-auto object-cover">
                                </a>
                            </div>
                        @else
                            <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200">
                                <i data-lucide="file-text" class="w-4 h-4 text-teal-500"></i>
                                <span>Lihat Dokumen Lampiran</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Conversation & Reply Section -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <i data-lucide="message-square" class="w-5 h-5 text-teal-500"></i>
                    <span>Tanggapan & Diskusi Petugas ({{ $complaint->replies->count() }})</span>
                </h3>

                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @forelse($complaint->replies as $reply)
                        <div class="p-4 rounded-xl {{ $reply->is_admin ? 'bg-teal-50/70 dark:bg-teal-950/30 border border-teal-200/60 dark:border-teal-800/60' : 'bg-slate-50 dark:bg-navy-950 border border-slate-200/60 dark:border-slate-800' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <i data-lucide="{{ $reply->is_admin ? 'shield-check' : 'user' }}" class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400"></i>
                                    {{ $reply->is_admin ? 'Petugas Perpustakaan' : 'Anda (Pengunjung)' }}
                                </span>
                                <span class="text-[11px] text-slate-400">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line pl-5">
                                {{ $reply->message }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 text-xs">
                            Belum ada pesan tanggapan untuk laporan ini. Staf kami sedang meninjau.
                        </div>
                    @endforelse
                </div>

                <!-- Form Balas Pesan -->
                <form action="{{ route('visitor.complaints.reply', $complaint->ticket_number) }}" method="POST" class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    @csrf
                    <div>
                        <label for="visitor_msg" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kirim Pesan Tambahan / Balasan ke Petugas
                        </label>
                        <textarea id="visitor_msg" name="message" rows="3" required placeholder="Tuliskan pesan atau konfirmasi Anda di sini..."
                            class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-xs text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-colors flex items-center gap-1.5">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Kirim Balasan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customer Satisfaction Rating Card (When resolved) -->
            @if($complaint->status === 'resolved')
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-navy-900 dark:to-navy-950 rounded-2xl p-6 sm:p-8 border border-amber-200 dark:border-amber-900/60 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-md">
                            <i data-lucide="star" class="w-5 h-5 fill-white"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Penilaian Kepuasan Anda</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400">Bagikan kepuasan Anda setelah laporan ini selesai ditangani.</p>
                        </div>
                    </div>

                    @if($complaint->satisfaction)
                        <div class="bg-white dark:bg-navy-900 p-4 rounded-xl border border-amber-200/80 dark:border-amber-900/50">
                            <div class="flex items-center gap-1 mb-1.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" class="w-4 h-4 {{ $i <= $complaint->satisfaction->rating ? 'text-amber-400 fill-amber-400' : 'text-slate-300' }}"></i>
                                @endfor
                                <span class="text-xs font-bold text-slate-800 dark:text-white ml-2">{{ $complaint->satisfaction->rating }} / 5 Bintang</span>
                            </div>
                            @if($complaint->satisfaction->comment)
                                <p class="text-xs text-slate-600 dark:text-slate-300 italic">"{{ $complaint->satisfaction->comment }}"</p>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('complaints.satisfaction', $complaint->ticket_number) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Tingkat Kepuasan (1 - 5 Bintang):</label>
                                <div class="flex items-center gap-3">
                                    @for($star = 1; $star <= 5; $star++)
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $star }}" {{ $star === 5 ? 'checked' : '' }} class="accent-amber-500">
                                            <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-0.5">
                                                {{ $star }} <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <div>
                                <textarea name="comment" rows="2" placeholder="Ulasan Anda mengenai keramahan & kecepatan penanganan..."
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-950 text-xs text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-amber-500"></textarea>
                            </div>

                            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 shadow-sm">
                                Kirim Penilaian
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Column: Status Timeline & Histories (Col 4) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-teal-500"></i>
                    <span>Linimasa Perubahan Status</span>
                </h3>

                <div class="relative pl-5 space-y-4 before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800 text-xs">
                    @foreach($complaint->statusHistories as $hist)
                        <div class="relative">
                            <div class="absolute -left-5 top-1 w-3 h-3 rounded-full bg-teal-500 border-2 border-white dark:border-navy-900"></div>
                            <p class="font-bold text-slate-800 dark:text-white uppercase text-[11px]">{{ $hist->new_status }}</p>
                            <p class="text-[10px] text-slate-400">{{ $hist->created_at->format('d/m/Y H:i') }} WIB</p>
                            @if($hist->notes)
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 bg-slate-50 dark:bg-navy-950 p-2 rounded border border-slate-200/50 dark:border-slate-800">
                                    {{ $hist->notes }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

