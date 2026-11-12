@extends('layouts.public')

@section('title', 'Lacak Progres Tiket Pengaduan – LibraryCare')

@section('content')
<div class="py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb & Header -->
        <div class="mb-8 text-center sm:text-left">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Lacak Status Laporan Pengaduan
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                Masukkan nomor tiket Anda untuk memantau proses tindak lanjut oleh tim perpustakaan secara transparan.
            </p>
        </div>

        <!-- Search Ticket Card -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 shadow-md border border-slate-200/80 dark:border-slate-800 mb-8">
            <form action="{{ route('complaints.track') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="ticket" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="ticket" value="{{ $ticket }}" placeholder="Masukkan Nomor Tiket (Contoh: LIB-2026-0001)" required
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-sm font-semibold uppercase tracking-wider focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                </div>
                <button type="submit" class="px-6 py-3 rounded-xl font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-500/20 transition-all flex items-center justify-center gap-2 text-sm shrink-0">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari Tiket</span>
                </button>
            </form>
        </div>

        @if(!empty($ticket) && !$complaint)
            <!-- Ticket Not Found Empty State -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-12 text-center border border-slate-200/80 dark:border-slate-800">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-500 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-octagon" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tiket Tidak Ditemukan</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto mt-2 leading-relaxed">
                    Nomor tiket <strong class="text-rose-600 font-mono">{{ $ticket }}</strong> tidak terdaftar di database kami. Pastikan format nomor tiket sudah sesuai atau ajukan keluhan baru.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('landing') }}#form-pengaduan" class="px-5 py-2.5 rounded-xl font-semibold text-white bg-teal-600 hover:bg-teal-700 text-sm">
                        Buat Pengaduan Baru
                    </a>
                    <a href="{{ route('complaints.track') }}" class="px-5 py-2.5 rounded-xl font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-sm">
                        Reset Pencarian
                    </a>
                </div>
            </div>
        @elseif($complaint)
            <!-- Ticket Found: Complaint Detail & Progress Timeline -->
            <div class="space-y-6">
                <!-- Main Header Box -->
                <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 shadow-md border border-slate-200/80 dark:border-slate-800">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor Tiket</span>
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-xs font-bold">{{ $complaint->ticket_number }}</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-2">{{ $complaint->title }}</h2>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                <span>Diajukan pada {{ $complaint->created_at->format('d M Y, H:i') }} WIB</span>
                                &bull;
                                <span>Kanal: {{ ucfirst(str_replace('_', ' ', $complaint->source)) }}</span>
                            </p>
                        </div>

                        <!-- Status Badge -->
                        <div class="shrink-0">
                            @php $badge = $complaint->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold {{ $badge['bg'] }}">
                                <i data-lucide="{{ $badge['icon'] }}" class="w-4 h-4"></i>
                                <span>{{ $badge['label'] }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Banner Tautkan Notifikasi Telegram Otomatis (Tanpa Perlu Login) -->
                    <div class="mt-4 p-4 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <i data-lucide="send" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Notifikasi Otomatis via Bot Telegram</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">
                                    @if(!empty($complaint->visitor_telegram))
                                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold">✅ Tiket ini telah terhubung ke Telegram (Chat ID: {{ $complaint->visitor_telegram }}). Setiap respon admin akan otomatis masuk.</span>
                                    @else
                                        Koneksikan tiket ini ke bot Telegram Anda dengan 1-klik agar setiap respon atau perubahan status dari petugas langsung masuk ke chat Anda.
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if(empty($complaint->visitor_telegram))
                            <a href="https://t.me/LibraryCare_bot?start={{ $complaint->ticket_number }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-sm transition-all shrink-0">
                                <i data-lucide="bell-ring" class="w-4 h-4"></i>
                                <span>Hubungkan ke Telegram</span>
                            </a>
                        @endif
                    </div>

                    <!-- Progress Step Indicator -->
                    <div class="py-8">
                        @php
                            $stepIndex = match($complaint->status) {
                                'pending' => 1,
                                'processing' => 2,
                                'resolved' => 3,
                                'rejected' => 0,
                                default => 1
                            };
                        @endphp

                        @if($complaint->status === 'rejected')
                            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
                                <i data-lucide="x-circle" class="w-5 h-5 shrink-0 text-rose-600"></i>
                                <span>Laporan ini telah ditutup atau ditolak oleh petugas. Silakan cek catatan di riwayat status.</span>
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-2 relative">
                                <!-- Step 1 -->
                                <div class="text-center relative">
                                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm mb-2 {{ $stepIndex >= 1 ? 'bg-teal-600 text-white ring-4 ring-teal-100 dark:ring-teal-950' : 'bg-slate-200 text-slate-500' }}">
                                        @if($stepIndex > 1) <i data-lucide="check" class="w-5 h-5"></i> @else 1 @endif
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">Menunggu</p>
                                    <p class="text-[11px] text-slate-500">Laporan Diterima</p>
                                </div>

                                <!-- Step 2 -->
                                <div class="text-center relative">
                                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm mb-2 {{ $stepIndex >= 2 ? 'bg-sky-600 text-white ring-4 ring-sky-100 dark:ring-sky-950' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                                        @if($stepIndex > 2) <i data-lucide="check" class="w-5 h-5"></i> @else 2 @endif
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">Diproses</p>
                                    <p class="text-[11px] text-slate-500">Tindak Lanjut Tim</p>
                                </div>

                                <!-- Step 3 -->
                                <div class="text-center relative">
                                    <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm mb-2 {{ $stepIndex >= 3 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                                        @if($stepIndex >= 3) <i data-lucide="check" class="w-5 h-5"></i> @else 3 @endif
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">Selesai</p>
                                    <p class="text-[11px] text-slate-500">Masalah Dituntaskan</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Complaint Description Content -->
                    <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori Laporan</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                                    {{ $complaint->category->name }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Deskripsi Keluhan</span>
                            <div class="mt-2 text-sm text-slate-700 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-navy-950 p-4 rounded-xl border border-slate-200/60 dark:border-slate-800 whitespace-pre-line">
                                {{ $complaint->description }}
                            </div>
                        </div>

                        @if($complaint->attachment)
                            <div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Lampiran Pendukung</span>
                                <div class="mt-2">
                                    @php $ext = pathinfo($complaint->attachment, PATHINFO_EXTENSION); @endphp
                                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 max-w-md">
                                            <img src="{{ asset('storage/' . $complaint->attachment) }}" alt="Lampiran" class="w-full h-auto object-cover max-h-72">
                                        </div>
                                    @else
                                        <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200">
                                            <i data-lucide="file" class="w-4 h-4 text-teal-500"></i>
                                            <span>Lihat / Unduh Dokumen Lampiran</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Change Timeline & History -->
                <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 shadow-md border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <i data-lucide="history" class="w-5 h-5 text-teal-500"></i>
                        <span>Riwayat Penanganan Laporan</span>
                    </h3>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                        @forelse($complaint->statusHistories as $history)
                            <div class="relative">
                                <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-teal-500 border-4 border-white dark:border-navy-900"></div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">
                                    {{ $history->created_at->format('d M Y, H:i') }} WIB
                                    @if($history->changer)
                                        &bull; oleh <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $history->changer->name }}</span>
                                    @endif
                                </div>
                                <div class="text-sm font-semibold text-slate-800 dark:text-white">
                                    Status: <span class="text-teal-600 dark:text-teal-400 uppercase font-mono">{{ $history->new_status }}</span>
                                </div>
                                @if($history->notes)
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1.5 bg-slate-50 dark:bg-navy-950 p-3 rounded-lg border border-slate-200/50 dark:border-slate-800">
                                        {{ $history->notes }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada riwayat perubahan status.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Conversation Thread / Tanggapan Petugas -->
                <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 shadow-md border border-slate-200/80 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-teal-500"></i>
                        <span>Percakapan & Tanggapan Petugas ({{ $complaint->replies->count() }})</span>
                    </h3>

                    <div class="space-y-4">
                        @forelse($complaint->replies as $reply)
                            <div class="p-4 rounded-xl {{ $reply->is_admin ? 'bg-teal-50/70 dark:bg-teal-950/30 border border-teal-200/60 dark:border-teal-800/60' : 'bg-slate-50 dark:bg-navy-950 border border-slate-200/60 dark:border-slate-800' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $reply->is_admin ? 'bg-teal-600 text-white' : 'bg-slate-300 dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                                            {{ $reply->is_admin ? 'P' : 'U' }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">
                                            {{ $reply->is_admin ? 'Petugas Perpustakaan' : ($reply->user->name ?? $complaint->visitor_name) }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400">{{ $reply->created_at->format('d M Y, H:i') }} WIB</span>
                                </div>
                                <p class="text-sm text-slate-700 dark:text-slate-300 pl-8 leading-relaxed whitespace-pre-line">
                                    {{ $reply->message }}
                                </p>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-500 text-sm">
                                Belum ada tanggapan untuk laporan ini. Petugas kami akan segera meninjau tiket Anda.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Satisfaction Rating Section -->
                @if($complaint->status === 'resolved')
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-navy-900 dark:to-navy-950 rounded-2xl p-6 sm:p-8 shadow-md border border-amber-200 dark:border-amber-800/60">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                                <i data-lucide="star" class="w-5 h-5 fill-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Penilaian Kepuasan Layanan</h3>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Laporan Anda telah dinyatakan Selesai. Mohon berikan masukan Anda.</p>
                            </div>
                        </div>

                        @if($complaint->satisfaction)
                            <div class="bg-white dark:bg-navy-900 p-5 rounded-xl border border-amber-200/80 dark:border-amber-900/50 mt-4">
                                <div class="flex items-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i data-lucide="star" class="w-5 h-5 {{ $i <= $complaint->satisfaction->rating ? 'text-amber-400 fill-amber-400' : 'text-slate-300' }}"></i>
                                    @endfor
                                    <span class="text-sm font-bold ml-2 text-slate-800 dark:text-white">{{ $complaint->satisfaction->rating }} / 5 Bintang</span>
                                </div>
                                @if($complaint->satisfaction->comment)
                                    <p class="text-sm text-slate-600 dark:text-slate-300 italic">"{{ $complaint->satisfaction->comment }}"</p>
                                @endif
                                <p class="text-xs text-slate-400 mt-2">Diberikan pada {{ $complaint->satisfaction->created_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                        @else
                            <form action="{{ route('complaints.satisfaction', $complaint->ticket_number) }}" method="POST" class="space-y-4 mt-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        Pilih Bintang Kepuasan (1 - 5)
                                    </label>
                                    <div class="flex items-center gap-3">
                                        @for($star = 1; $star <= 5; $star++)
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $star }}" {{ $star === 5 ? 'checked' : '' }} class="accent-amber-500">
                                                <span class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-0.5">
                                                    {{ $star }} <i data-lucide="star" class="w-4 h-4 text-amber-400 fill-amber-400"></i>
                                                </span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div>
                                    <label for="comment" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        Ulasan atau Komentar Anda (Opsional)
                                    </label>
                                    <textarea id="comment" name="comment" rows="3" placeholder="Bagikan pengalaman Anda terkait kecepatan dan kualitas tindak lanjut petugas..."
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-950 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none"></textarea>
                                </div>

                                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-amber-500 hover:bg-amber-600 shadow-md transition-colors text-sm">
                                    Kirim Ulasan Kepuasan
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

