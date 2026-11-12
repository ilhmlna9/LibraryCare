@extends('layouts.public')

@section('title', 'LibraryCare – Layanan Pengaduan & Saran Pengunjung Perpustakaan')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
    <!-- Subtle Background Accents -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-b from-teal-500/10 via-transparent to-transparent pointer-events-none -z-10 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800">
                    <span class="flex h-2 w-2 rounded-full bg-teal-500"></span>
                    <span>Sistem CRM Layanan Perpustakaan</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                    Suara Anda Membangun <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-sky-600 dark:from-teal-400 dark:to-sky-400">Perpustakaan Impian</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Sampaikan keluhan fasilitas, kendala sirkulasi buku, maupun usulan literasi secara mudah. Setiap laporan dipantau transparan dan ditindaklanjuti cepat dengan notifikasi WhatsApp & Telegram.
                </p>

                <!-- Hero CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="#form-pengaduan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl font-semibold text-white bg-teal-600 hover:bg-teal-700 dark:bg-teal-600 dark:hover:bg-teal-500 shadow-lg shadow-teal-600/20 hover:shadow-teal-600/30 hover:-translate-y-0.5 transition-all">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                        <span>Kirim Keluhan Sekarang</span>
                    </a>
                    <a href="#kanal-pengaduan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-5 py-3.5 rounded-xl font-semibold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/70 border border-teal-200 dark:border-teal-800 hover:bg-teal-100 transition-all shadow-sm">
                        <i data-lucide="radio" class="w-5 h-5"></i>
                        <span>Pilih 4 Saluran (WA, TG, GForms)</span>
                    </a>
                    <a href="{{ route('complaints.track') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:-translate-y-0.5 transition-all shadow-sm">
                        <i data-lucide="search" class="w-5 h-5 text-teal-600 dark:text-teal-400"></i>
                        <span>Lacak Tiket</span>
                    </a>
                </div>

                <!-- Live Quick Stats -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-200/80 dark:border-slate-800/80 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Laporan Masuk</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-teal-600 dark:text-teal-400">{{ $stats['resolved'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Selesai Ditangani</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-center lg:justify-start gap-1">
                            <span class="text-2xl sm:text-3xl font-extrabold text-amber-500">{{ $stats['avg_satisfaction'] }}</span>
                            <i data-lucide="star" class="w-5 h-5 fill-amber-400 text-amber-400"></i>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Indeks Kepuasan</p>
                    </div>
                </div>
            </div>

            <!-- Right Hero Card: Quick Ticket Tracker -->
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="100">
                <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-800 relative">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="ticket" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Cek Status Tiket Anda</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pantau tindak lanjut secara langsung tanpa login</p>
                        </div>
                    </div>

                    <form action="{{ route('complaints.track') }}" method="GET" class="space-y-4">
                        <div>
                            <label for="ticket_search" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                                Masukkan Nomor Tiket
                            </label>
                            <div class="relative">
                                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" id="ticket_search" name="ticket" placeholder="Contoh: LIB-2026-0001" required
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none uppercase tracking-wider font-semibold">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl font-semibold text-white bg-slate-900 dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 transition-colors flex items-center justify-center gap-2 text-sm shadow-md">
                            <span>Periksa Status Pengaduan</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="bell" class="w-3.5 h-3.5 text-teal-500"></i>
                            Notifikasi WA Otomatis
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Privasi Data Terjamin
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section: Alur Pengaduan -->
<section id="alur" class="py-16 sm:py-24 bg-white dark:bg-navy-900/60 border-y border-slate-200/80 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Proses Transparan</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mt-2">
                Bagaimana Pengaduan Anda Ditangani?
            </h2>
            <p class="text-slate-600 dark:text-slate-400 mt-3 text-sm sm:text-base">
                Setiap laporan yang masuk diverifikasi dan ditindaklanjuti secara sistematis dengan pembaruan status berkala.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <!-- Step 1 -->
            <div class="bg-slate-50 dark:bg-navy-950 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 relative hover:-translate-y-1 transition-all shadow-sm" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-lg mb-5 shadow-md shadow-teal-500/20">
                    1
                </div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">Kirim Laporan</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Tuliskan keluhan atau saran melalui formulir web, WhatsApp, Telegram, atau Google Forms.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-slate-50 dark:bg-navy-950 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 relative hover:-translate-y-1 transition-all shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 rounded-xl bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-bold text-lg mb-5 shadow-md">
                    2
                </div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">Dapatkan Nomor Tiket</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Sistem menerbitkan nomor tiket unik (contoh: <code class="text-teal-600 dark:text-teal-400 font-semibold">LIB-2026-0001</code>) untuk pelacakan.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-slate-50 dark:bg-navy-950 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 relative hover:-translate-y-1 transition-all shadow-sm" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold text-lg mb-5 shadow-md shadow-sky-500/20">
                    3
                </div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">Tindak Lanjut Petugas</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Admin perpustakaan memproses masalah Anda dan mengirim balasan atau pembaruan status ke WhatsApp Anda.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bg-slate-50 dark:bg-navy-950 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 relative hover:-translate-y-1 transition-all shadow-sm" data-aos="fade-up" data-aos-delay="400">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg mb-5 shadow-md shadow-emerald-500/20">
                    4
                </div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">Selesai & Rating Kepuasan</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Laporan selesai dituntaskan, dan Anda dapat memberikan ulasan bintang atas kinerja pelayanan petugas.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section: Kategori Keluhan -->
<section id="kategori" class="py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Klasifikasi Layanan</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mt-2">
                Kategori Masalah yang Dapat Dilaporkan
            </h2>
            <p class="text-slate-600 dark:text-slate-400 mt-3 text-sm sm:text-base">
                Pilih kategori yang sesuai agar petugas bidang terkait dapat langsung menangani laporan Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="p-6 rounded-2xl bg-white dark:bg-navy-900 border border-slate-200/80 dark:border-slate-800 hover:border-teal-500 dark:hover:border-teal-500/50 transition-all hover:-translate-y-1 shadow-sm" data-aos="fade-up">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-teal-600 dark:text-teal-400 mb-4">
                        <i data-lucide="{{ $category->icon ?: 'tag' }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $category->name }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $category->description }}</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">{{ $category->complaints()->count() }} laporan tercatat</span>
                        <a href="#form-pengaduan" onclick="document.getElementById('category_id').value = '{{ $category->id }}'" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1">
                            Lapor kategori ini &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Section: Integrasi Multi-Kanal (WhatsApp, Telegram, Google Forms) -->
<section id="kanal-pengaduan" class="py-16 sm:py-20 bg-slate-900 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-400">Pilihan Saluran Pengaduan</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-2 text-white">
                Sampaikan Aspirasi Lewat Aplikasi Pilihan Anda
            </h2>
            <p class="text-slate-300 mt-3 text-sm sm:text-base">
                Anda bebas menyampaikan keluhan melalui 4 kanal di bawah ini. Semua data otomatis bermuara ke <strong>Sistem Terpusat Admin Perpustakaan</strong> dan langsung diterbitkan Nomor Tiket resmi!
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- 1. Kanal Website Form -->
            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex flex-col justify-between hover:border-teal-500/60 transition-all hover:-translate-y-1">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center">
                        <i data-lucide="globe" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white">Formulir Web</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Isi form resmi di website ini secara lengkap, dapat melampirkan berkas bukti (foto/PDF), dan langsung cek tiket secara real-time.
                    </p>
                </div>
                <div class="pt-6">
                    <a href="#form-pengaduan" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-500 text-white transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        <span>Isi Form Web &darr;</span>
                    </a>
                </div>
            </div>

            <!-- 2. WhatsApp Card -->
            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex flex-col justify-between hover:border-emerald-500/60 transition-all hover:-translate-y-1">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <i data-lucide="message-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">WhatsApp Chat</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800">24 Jam</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Sampaikan keluhan, kritik, atau saran langsung ke kontak WhatsApp resmi layanan perpustakaan.
                    </p>
                    <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 text-[11px] text-emerald-300 flex items-center justify-between">
                        <span>📱 Kontak Resmi:</span>
                        <span class="text-white font-mono font-bold tracking-wide">0882-1209-3276</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800 font-mono text-[10px] text-emerald-300">
                        Kode: <span class="text-white">#LAPOR Judul | Rincian keluhan</span>
                    </div>
                </div>
                <div class="pt-4">
                    @php
                        $cleanWA = preg_replace('/[^0-9]/', '', $settings['library_whatsapp'] ?? '088212093276');
                        if (str_starts_with($cleanWA, '0')) {
                            $cleanWA = '62' . substr($cleanWA, 1);
                        }
                    @endphp
                    <button type="button" onclick="openWhatsAppModal()" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors shadow-sm">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>Pilih Topik & Chat WhatsApp</span>
                    </button>
                </div>
            </div>

            <!-- 3. Telegram Card -->
            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex flex-col justify-between hover:border-sky-500/60 transition-all hover:-translate-y-1">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center">
                        <i data-lucide="send" class="w-6 h-6"></i>
                    </div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Telegram Bot</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-950 text-sky-400 border border-sky-800 font-mono">@Bot</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Kirim pesan ke bot Telegram perpustakaan. Cukup ketik perintah <code>/lapor</code> untuk membuat keluhan baru atau <code>/status</code> untuk melacak tiket.
                    </p>
                    <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 font-mono text-[10px] text-sky-300">
                        Perintah: <span class="text-white">/lapor Judul | Keluhan</span>
                    </div>
                </div>
                <div class="pt-4">
                    @php
                        $tgUsername = ltrim($settings['library_telegram'] ?? '@LibraryCareBot', '@');
                    @endphp
                    <a href="https://t.me/{{ $tgUsername }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white transition-colors shadow-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Buka Bot Telegram</span>
                    </a>
                </div>
            </div>

            <!-- 4. Google Forms Card -->
            <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex flex-col justify-between hover:border-purple-500/60 transition-all hover:-translate-y-1">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                    </div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Google Forms</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-950 text-purple-400 border border-purple-800">Auto Sync</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Cocok untuk pengisian kuesioner dari tablet perpustakaan atau link survei. Setiap submit otomatis sinkron menjadi tiket resmi via Webhook.
                    </p>
                    <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 font-mono text-[10px] text-purple-300">
                        Apps Script &rarr; Webhook CRM
                    </div>
                </div>
                <div class="pt-4 space-y-2">
                    <a href="{{ route('google-forms.simulate') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white transition-colors shadow-sm">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        <span>Buka Formulir Google Forms</span>
                    </a>
                    @if(!empty($settings['google_forms_url']))
                        <a href="{{ $settings['google_forms_url'] }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl text-[11px] font-semibold text-purple-400 hover:text-purple-300 hover:bg-slate-950/50 transition-colors">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Buka di docs.google.com</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section: Form Pengaduan Website -->
<section id="form-pengaduan" class="py-12 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Formulir Resmi</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1.5">
                Kirimkan Keluhan, Kritik, atau Saran
            </h2>
            <p class="text-slate-600 dark:text-slate-400 mt-2 text-xs sm:text-sm">
                Isi rincian pengaduan Anda di bawah ini. Nomor tiket pelacakan langsung muncul di layar secara instan dan admin segera menerima pemberitahuan.
            </p>
        </div>

        <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 shadow-lg border border-slate-200/80 dark:border-slate-800" data-aos="fade-up">
            <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Data Pelapor: Grid 2 Kolom -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="visitor_name" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="visitor_name" name="visitor_name" value="{{ old('visitor_name') }}" required
                            placeholder="Contoh: Muhammad Rafli"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all @error('visitor_name') border-rose-500 ring-1 ring-rose-500 @enderror">
                        @error('visitor_name')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="visitor_email" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                            Alamat Email <span class="text-[11px] font-normal text-slate-500">(Opsional)</span>
                        </label>
                        <input type="email" id="visitor_email" name="visitor_email" value="{{ old('visitor_email') }}"
                            placeholder="nama@student.ac.id (opsional)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all @error('visitor_email') border-rose-500 ring-1 ring-rose-500 @enderror">
                        @error('visitor_email')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kontak WhatsApp & Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="visitor_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                            Nomor WhatsApp <span class="text-xs font-normal text-slate-500">(Kontak Pelapor)</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="phone" class="w-4 h-4 text-emerald-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="tel" id="visitor_phone" name="visitor_phone" value="{{ old('visitor_phone', '088212093276') }}"
                                placeholder="088212093276"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all @error('visitor_phone') border-rose-500 ring-1 ring-rose-500 @enderror">
                        </div>
                        @error('visitor_phone')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                            Kategori Pengaduan <span class="text-rose-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all @error('category_id') border-rose-500 ring-1 ring-rose-500 @enderror">
                            <option value="">-- Pilih Kategori Masalah --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Judul Laporan -->
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                        Judul Laporan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        placeholder="Contoh: AC di Ruang Referensi Lantai 2 Kurang Dingin"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all @error('title') border-rose-500 ring-1 ring-rose-500 @enderror">
                    @error('title')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Laporan -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                        Deskripsi Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="3" required
                        placeholder="Jelaskan secara rinci kendala yang Anda alami, lokasi spesifik, atau usulan perbaikan..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all @error('description') border-rose-500 ring-1 ring-rose-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lampiran Dokumen/Foto -->
                <div>
                    <label for="attachment" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                        Lampiran Foto atau Dokumen Pendukung <span class="text-[11px] font-normal text-slate-500">(Opsional, Maks. 5 MB)</span>
                    </label>
                    <div class="relative flex items-center justify-center w-full">
                        <label for="attachment" class="flex flex-col items-center justify-center w-full py-4 px-4 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl cursor-pointer bg-slate-50 dark:bg-navy-950 hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-colors">
                            <div class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                                <i data-lucide="upload-cloud" class="w-5 h-5 text-teal-600 dark:text-teal-400 shrink-0"></i>
                                <span><strong class="text-teal-600 dark:text-teal-400">Pilih berkas</strong> atau seret ke sini (JPG, PNG, PDF maks 5MB)</span>
                            </div>
                            <input id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="document.getElementById('file-chosen-text').textContent = this.files[0] ? 'Berkas dipilih: ' + this.files[0].name : ''">
                        </label>
                    </div>
                    <p id="file-chosen-text" class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold mt-1"></p>
                    @error('attachment')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-5 rounded-xl font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 transition-all text-xs flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pengaduan & Dapatkan Nomor Tiket</span>
                    </button>
                    <p class="text-center text-[10px] text-slate-400 mt-2">
                        Dengan mengirimkan laporan, Anda menyetujui ketentuan privasi data layanan perpustakaan.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Section: FAQ -->
<section id="faq" class="py-16 sm:py-24 bg-white dark:bg-navy-900/60 border-t border-slate-200/80 dark:border-slate-800 transition-colors">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Pusat Bantuan</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mt-2">
                Pertanyaan yang Sering Diajukan (FAQ)
            </h2>
            <p class="text-slate-600 dark:text-slate-400 mt-3 text-sm sm:text-base">
                Temukan jawaban cepat terkait prosedur penyampaian keluhan dan tindak lanjut laporan di perpustakaan.
            </p>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 bg-slate-50/50 dark:bg-navy-950">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-teal-500 shrink-0"></i>
                    Berapa lama laporan keluhan akan ditindaklanjuti?
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2.5 pl-7 leading-relaxed">
                    Setiap laporan yang masuk akan ditinjau oleh staf perpustakaan dalam waktu maksimal 1x24 jam kerja. Untuk kendala darurat seperti fasilitas listrik atau AC, penanganan teknis segera dikoordinasikan di hari yang sama.
                </p>
            </div>

            <!-- FAQ 2 -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 bg-slate-50/50 dark:bg-navy-950">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-teal-500 shrink-0"></i>
                    Apakah saya harus memiliki akun untuk membuat laporan?
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2.5 pl-7 leading-relaxed">
                    Tidak wajib. Pengunjung umum atau mahasiswa tetap dapat membuat laporan tanpa login dan melacaknya menggunakan nomor tiket. Namun dengan mendaftar akun, Anda dapat melihat riwayat semua laporan dan membalas tanggapan admin lebih mudah.
                </p>
            </div>

            <!-- FAQ 3 -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 bg-slate-50/50 dark:bg-navy-950">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-teal-500 shrink-0"></i>
                    Bagaimana cara menghubungkan notifikasi Telegram?
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2.5 pl-7 leading-relaxed">
                    Buka aplikasi Telegram Anda, cari bot resmi kami <code>{{ $settings['library_telegram'] }}</code>, ketik <code>/start</code> untuk memperoleh Chat ID Anda, kemudian simpan ID tersebut pada menu Profil Akun di website LibraryCare.
                </p>
            </div>

            <!-- FAQ 4 -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 bg-slate-50/50 dark:bg-navy-950">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-teal-500 shrink-0"></i>
                    Apakah keluhan melalui Google Forms tetap mendapatkan nomor tiket?
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2.5 pl-7 leading-relaxed">
                    Ya! Setiap tanggapan formulir di Google Forms akan otomatis diproses oleh sistem backend kami melalui webhook Google Apps Script, dibuatkan nomor tiket resmi, dan diteruskan ke petugas jaga.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section: Info Kontak Perpustakaan -->
<section class="py-16 bg-slate-100 dark:bg-navy-950 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-navy-900 rounded-3xl p-8 sm:p-12 border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Lokasi Layanan</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                            {{ $settings['library_address'] }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Jam Operasional</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                            {{ $settings['library_hours'] }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <i data-lucide="phone-call" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Kontak Langsung</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                            Telepon: {{ $settings['library_phone'] }}<br>
                            WhatsApp: {{ $settings['library_whatsapp'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Pilihan Topik Pengaduan WhatsApp -->
<div id="whatsapp-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-navy-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl transition-all">
        <!-- Close Button -->
        <button type="button" onclick="closeWhatsAppModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- Header -->
        <div class="flex items-center gap-3.5 mb-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <i data-lucide="message-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pilih Topik Pengaduan WhatsApp</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pesan otomatis dengan format resmi siap dikirim ke bot perpustakaan.</p>
            </div>
        </div>

        <div class="space-y-4 text-left">
            <!-- 1. Nama Pengunjung -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Nama Pengunjung / Mahasiswa
                </label>
                <input type="text" id="wa-input-name" 
                    value="{{ Auth::check() ? Auth::user()->name : '' }}" 
                    placeholder="Nama Anda (Opsional)" 
                    class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                <p class="text-[10px] text-slate-400 mt-1">
                    *Jika dikosongkan, nama profil WhatsApp Anda otomatis diambil oleh sistem bot.
                </p>
            </div>

            <!-- 2. Pilih Kategori Topik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Pilih Kategori Masalah <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/40 hover:border-emerald-500 cursor-pointer transition-all">
                            <input type="radio" name="wa_category" value="{{ $cat->name }}" {{ $loop->first ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 3. Rincian Kendala Singkat -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Rincian Kendala / Laporan <span class="text-rose-500">*</span>
                </label>
                <textarea id="wa-input-desc" rows="2" 
                    placeholder="Contoh: AC di lantai 2 sebelah barat mati total" 
                    class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none resize-none"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex flex-col sm:flex-row gap-2.5">
                <button type="button" onclick="submitToWhatsApp()" class="flex-1 inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition-all shadow-md hover:shadow-lg">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Buka WhatsApp & Kirim</span>
                </button>
                <button type="button" onclick="closeWhatsAppModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openWhatsAppModal() {
        const modal = document.getElementById('whatsapp-modal');
        if (modal) {
            modal.classList.remove('hidden');
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    }

    function closeWhatsAppModal() {
        const modal = document.getElementById('whatsapp-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function submitToWhatsApp() {
        const selectedCat = document.querySelector('input[name="wa_category"]:checked');
        const catName = selectedCat ? selectedCat.value : 'Fasilitas & Ruangan';
        const name = document.getElementById('wa-input-name').value.trim();
        const desc = document.getElementById('wa-input-desc').value.trim() || 'Mohon bantuan tindak lanjut fasilitas perpustakaan.';

        let msg = `#LAPOR [${catName}] | ${desc}`;
        if (name) {
            msg += ` | Nama: ${name}`;
        }

        const waNum = '{{ $cleanWA }}';
        const waUrl = `https://wa.me/${waNum}?text=${encodeURIComponent(msg)}`;
        
        closeWhatsAppModal();
        window.open(waUrl, '_blank');
    }

    // Tutup modal jika user klik di area luar modal
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('whatsapp-modal');
        if (modal && e.target === modal) {
            closeWhatsAppModal();
        }
    });
</script>
@endsection

