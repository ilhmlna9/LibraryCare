@extends('layouts.admin')

@section('title', 'Detail Pengaduan ' . $complaint->ticket_number)
@section('header_title', 'Detail & Tindak Lanjut: ' . $complaint->ticket_number)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-navy-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.complaints.index') }}" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <span class="font-mono text-base font-bold text-slate-900 dark:text-white">{{ $complaint->ticket_number }}</span>
                @php $badge = $complaint->status_badge; @endphp
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $badge['bg'] }}">
                    <i data-lucide="{{ $badge['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $badge['label'] }}</span>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 pl-7">
                Diterima via <strong class="text-slate-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $complaint->source)) }}</strong> pada {{ $complaint->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('complaints.track', ['ticket' => $complaint->ticket_number]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Tampilan Pengunjung</span>
            </a>
            <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900 transition-colors">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Details & Thread (Col 7) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Complaint Detail Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div>
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 mb-2">
                        {{ $complaint->category->name }}
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-snug">{{ $complaint->title }}</h2>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
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
                                    <img src="{{ asset('storage/' . $complaint->attachment) }}" alt="Lampiran" class="w-full h-auto object-cover hover:scale-105 transition-transform">
                                </a>
                            </div>
                        @else
                            <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200">
                                <i data-lucide="file-text" class="w-4 h-4 text-teal-500"></i>
                                <span>Unduh / Buka Dokumen Lampiran</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Conversation & Reply Section -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-teal-500"></i>
                        <span>Percakapan & Tanggapan Petugas ({{ $complaint->replies->count() }})</span>
                    </h3>
                </div>

                <!-- Messages Thread -->
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @forelse($complaint->replies as $reply)
                        <div class="p-4 rounded-xl {{ $reply->is_admin ? 'bg-teal-50/80 dark:bg-teal-950/30 border border-teal-200/70 dark:border-teal-800/60' : 'bg-slate-50 dark:bg-navy-950 border border-slate-200/60 dark:border-slate-800' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <i data-lucide="{{ $reply->is_admin ? 'shield-check' : 'user' }}" class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400"></i>
                                    {{ $reply->is_admin ? ($reply->user->name ?? 'Admin Perpustakaan') : ($reply->user->name ?? $complaint->visitor_name) }}
                                </span>
                                <span class="text-[11px] text-slate-400">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $reply->message }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 text-xs">
                            Belum ada tanggapan yang dikirim untuk tiket ini.
                        </div>
                    @endforelse
                </div>

                <!-- Reply Form -->
                <form action="{{ route('admin.complaints.reply', $complaint->id) }}" method="POST" class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    @csrf
                    <div>
                        <label for="message" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tulis Tanggapan Resmi
                        </label>
                        <textarea id="message" name="message" rows="3" required placeholder="Tuliskan respon atau penjelasan perbaikan untuk pengunjung..."
                            class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-xs text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500"></textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 dark:text-slate-400">
                            <input type="checkbox" name="send_whatsapp" value="1" checked class="rounded border-slate-300 dark:border-slate-700 text-teal-600 focus:ring-teal-500">
                            <span>Kirim notifikasi ke WhatsApp pengunjung ({{ $complaint->visitor_phone ?? 'Tidak ada nomor' }})</span>
                        </label>

                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 transition-colors shadow-sm flex items-center gap-1.5">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Kirim Tanggapan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Logs Audit Trail -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="bell" class="w-4 h-4 text-teal-500"></i>
                    <span>Log Notifikasi Keluar (Audit Trail)</span>
                </h3>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($complaint->notificationLogs as $log)
                        <div class="py-2.5 flex items-start justify-between gap-2">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-white uppercase text-[10px]">{{ $log->type }}</span>
                                <span class="text-slate-500 text-[11px]">&rarr; {{ $log->recipient }}</span>
                                <p class="text-slate-600 dark:text-slate-400 text-[11px] mt-0.5 truncate max-w-md">{{ $log->message }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $log->status === 'sent' ? 'bg-emerald-100 text-emerald-800' : ($log->status === 'simulated' ? 'bg-purple-100 text-purple-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $log->created_at->format('d/m H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-3 text-slate-500 text-xs text-center">Belum ada riwayat notifikasi tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Status Control, Visitor Info, History (Col 5) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Status Update Action Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="sliders" class="w-4 h-4 text-teal-500"></i>
                    <span>Perbarui Status Laporan</span>
                </h3>

                <form action="{{ route('admin.complaints.status', $complaint->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Status Penanganan <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" class="w-full p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
                            <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                            <option value="processing" {{ $complaint->status === 'processing' ? 'selected' : '' }}>⚙️ Sedang Diproses Petugas</option>
                            <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>✅ Selesai Ditangani</option>
                            <option value="rejected" {{ $complaint->status === 'rejected' ? 'selected' : '' }}>❌ Ditolak / Tidak Valid</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan Tindak Lanjut / Keterangan
                        </label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Teknisi AC telah membersihkan filter dan mengencangkan baut blower..."
                            class="w-full p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-xs text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500"></textarea>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-navy-950 border border-slate-200/60 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-400 space-y-1">
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Notifikasi Otomatis yang Dikirim:</p>
                        <p class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>WhatsApp: {{ $complaint->visitor_phone ? 'Aktif (' . $complaint->visitor_phone . ')' : 'Tidak ada nomor' }}</span>
                        </p>
                        <p class="flex items-center gap-1.5 text-sky-600 dark:text-sky-400">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Telegram: {{ ($complaint->visitor_telegram ?? $complaint->user?->telegram_chat_id) ? 'Aktif (' . ($complaint->visitor_telegram ?? $complaint->user?->telegram_chat_id) . ')' : 'Tidak ada Chat ID' }}</span>
                        </p>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 shadow-sm transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan & Kirim Notifikasi</span>
                    </button>
                </form>
            </div>

            <!-- Visitor Profile Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-teal-500"></i>
                    <span>Informasi Pengunjung</span>
                </h3>

                <div class="flex items-center gap-3 pt-1">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr($complaint->visitor_name, 0, 1)) }}
                    </div>
                    <div class="truncate text-xs">
                        <p class="font-bold text-slate-900 dark:text-white truncate">{{ $complaint->visitor_name }}</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $complaint->user_id ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $complaint->user_id ? 'Pengguna Terdaftar' : 'Tamu (Guest)' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Email:</span>
                        <span class="font-medium text-slate-800 dark:text-white">{{ $complaint->visitor_email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">WhatsApp:</span>
                        <span class="font-medium text-slate-800 dark:text-white">{{ $complaint->visitor_phone ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Telegram Chat ID:</span>
                        <span class="font-medium text-slate-800 dark:text-white font-mono">{{ $complaint->visitor_telegram ?? $complaint->user?->telegram_chat_id ?? 'Tidak Disertakan' }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Satisfaction (If submitted) -->
            @if($complaint->satisfaction)
                <div class="bg-amber-50 dark:bg-navy-900 rounded-2xl p-6 border border-amber-200 dark:border-amber-900/60 shadow-sm space-y-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="star" class="w-4 h-4 text-amber-500 fill-amber-500"></i>
                        <span>Ulasan Kepuasan Pengunjung</span>
                    </h3>
                    <div class="flex items-center gap-1 pt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $complaint->satisfaction->rating ? 'text-amber-400 fill-amber-400' : 'text-slate-300' }}"></i>
                        @endfor
                        <span class="text-xs font-bold text-slate-800 dark:text-white ml-2">{{ $complaint->satisfaction->rating }}/5.0</span>
                    </div>
                    @if($complaint->satisfaction->comment)
                        <p class="text-xs text-slate-700 dark:text-slate-300 italic pt-1">"{{ $complaint->satisfaction->comment }}"</p>
                    @endif
                </div>
            @endif

            <!-- Status History Timeline Card -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-teal-500"></i>
                    <span>Riwayat Perubahan Status</span>
                </h3>

                <div class="relative pl-5 space-y-4 before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800 text-xs">
                    @foreach($complaint->statusHistories as $hist)
                        <div class="relative">
                            <div class="absolute -left-5 top-1 w-3 h-3 rounded-full bg-teal-500 border-2 border-white dark:border-navy-900"></div>
                            <p class="font-bold text-slate-800 dark:text-white uppercase text-[11px]">{{ $hist->new_status }}</p>
                            <p class="text-[10px] text-slate-400">{{ $hist->created_at->format('d/m/Y H:i') }} WIB &bull; {{ $hist->changer->name ?? 'Sistem' }}</p>
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

