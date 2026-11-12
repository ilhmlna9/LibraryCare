@extends('layouts.public')

@section('title', 'Formulir Pengaduan Pengunjung – Google Forms Integration')

@section('content')
<div class="min-h-screen bg-purple-50/50 dark:bg-navy-950 py-10 sm:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header / Nav Back -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Website LibraryCare</span>
            </a>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 border border-purple-300 dark:border-purple-700">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                Google Forms Webhook Integration
            </span>
        </div>

        <!-- Google Forms Authentic Header Card -->
        <div class="bg-white dark:bg-navy-900 rounded-2xl border-t-8 border-t-purple-600 border-x border-b border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-md mb-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Formulir Aspirasi & Keluhan Pengunjung Perpustakaan
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                        Formulir ini terhubung secara otomatis ke sistem CRM LibraryCare melalui <strong>Google Apps Script & Webhook</strong>. Setiap tanggapan yang Anda kirim akan langsung diproses dan menerbitkan Nomor Tiket resmi pelacakan.
                    </p>
                </div>
                <div class="hidden sm:flex w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 items-center justify-center shrink-0">
                    <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between text-[11px] text-slate-500 gap-2">
                <div class="flex items-center gap-2 font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Endpoint: {{ $webhookUrl }}</span>
                </div>
                <span class="text-purple-600 dark:text-purple-400 font-semibold">* Wajib diisi</span>
            </div>
        </div>

        <!-- Success Alert Container -->
        <div id="gform-alert-success" class="hidden mb-6 p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-800 shadow-md">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="check" class="w-5 h-5"></i>
                </div>
                <div class="space-y-2 flex-1">
                    <h3 class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Tanggapan Berhasil Dikirim ke LibraryCare!</h3>
                    <p class="text-xs text-emerald-800 dark:text-emerald-300 leading-relaxed" id="gform-alert-message">
                        Data berhasil diterima melalui Webhook dan nomor tiket telah diterbitkan.
                    </p>
                    <div class="pt-2 flex flex-wrap gap-2">
                        <a id="btn-track-ticket" href="#" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            <span>Lacak Tiket Ini Sekarang</span>
                        </a>
                        <button type="button" onclick="resetGForm()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-navy-900 border border-slate-200 dark:border-slate-700 hover:bg-slate-50">
                            Kirim Jawaban Lain
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Alert Container -->
        <div id="gform-alert-error" class="hidden mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-rose-500"></i>
            <span id="gform-error-message">Terjadi kesalahan saat memproses formulir.</span>
        </div>

        <!-- Form Questions (Card-based like Google Forms) -->
        <form id="library-google-form" onsubmit="submitViaGFormsWebhook(event)" class="space-y-4">
            
            <!-- Question 1: Nama Lengkap -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-2 focus-within:border-purple-500 transition-all">
                <label for="gform_visitor_name" class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    Nama Lengkap Mahasiswa / Pengunjung <span class="text-rose-500">*</span>
                </label>
                <p class="text-[11px] text-slate-400">Tuliskan nama Anda sesuai kartu identitas perpustakaan atau KTM.</p>
                <input type="text" id="gform_visitor_name" name="visitor_name" required placeholder="Jawaban Anda"
                    class="w-full pt-2 pb-1 text-sm border-b-2 border-slate-200 dark:border-slate-700 focus:border-purple-600 dark:focus:border-purple-400 bg-transparent text-slate-900 dark:text-white outline-none transition-colors">
            </div>

            <!-- Question 2: Email -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-2 focus-within:border-purple-500 transition-all">
                <label for="gform_visitor_email" class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    Alamat Email Aktif <span class="text-rose-500">*</span>
                </label>
                <p class="text-[11px] text-slate-400">Email untuk konfirmasi dan tanggapan dari pengelola perpustakaan.</p>
                <input type="email" id="gform_visitor_email" name="visitor_email" required placeholder="contoh@kampus.ac.id"
                    class="w-full pt-2 pb-1 text-sm border-b-2 border-slate-200 dark:border-slate-700 focus:border-purple-600 dark:focus:border-purple-400 bg-transparent text-slate-900 dark:text-white outline-none transition-colors">
            </div>

            <!-- Question 3: Nomor WhatsApp -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-2 focus-within:border-purple-500 transition-all">
                <label for="gform_visitor_phone" class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    Nomor WhatsApp <span class="text-rose-500">*</span>
                </label>
                <p class="text-[11px] text-slate-400">Kode tiket dan notifikasi pembaruan penanganan otomatis dikirimkan ke nomor WhatsApp ini.</p>
                <input type="tel" id="gform_visitor_phone" name="visitor_phone" required placeholder="Contoh: 081234567890"
                    class="w-full pt-2 pb-1 text-sm border-b-2 border-slate-200 dark:border-slate-700 focus:border-purple-600 dark:focus:border-purple-400 bg-transparent text-slate-900 dark:text-white outline-none transition-colors">
            </div>

            <!-- Question 4: Kategori Keluhan -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-3 focus-within:border-purple-500 transition-all">
                <div>
                    <label class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                        Kategori Laporan / Masalah <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-400">Pilih bidang fasilitas atau pelayanan yang ingin dilaporkan.</p>
                </div>

                <div class="space-y-2.5 pt-1">
                    @foreach($categories as $category)
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                            <input type="radio" name="category_id" value="{{ $category->id }}" {{ $loop->first ? 'checked' : '' }}
                                class="text-purple-600 focus:ring-purple-500 w-4 h-4">
                            <div>
                                <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $category->name }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $category->description }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Question 5: Judul Laporan -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-2 focus-within:border-purple-500 transition-all">
                <label for="gform_title" class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    Judul Ringkas Pengaduan <span class="text-rose-500">*</span>
                </label>
                <p class="text-[11px] text-slate-400">Contoh: AC Lantai 2 Kurang Dingin, Usulan Pengadaan Buku AI, dll.</p>
                <input type="text" id="gform_title" name="title" required placeholder="Jawaban Anda"
                    class="w-full pt-2 pb-1 text-sm border-b-2 border-slate-200 dark:border-slate-700 focus:border-purple-600 dark:focus:border-purple-400 bg-transparent text-slate-900 dark:text-white outline-none transition-colors">
            </div>

            <!-- Question 6: Deskripsi Lengkap -->
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-2 focus-within:border-purple-500 transition-all">
                <label for="gform_description" class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                    Uraian / Deskripsi Rinci Masalah <span class="text-rose-500">*</span>
                </label>
                <p class="text-[11px] text-slate-400">Sertakan informasi lokasi ruangan, waktu kejadian, atau detail judul buku yang dimaksud.</p>
                <textarea id="gform_description" name="description" rows="4" required placeholder="Jawaban Anda"
                    class="w-full pt-2 pb-1 text-sm border-b-2 border-slate-200 dark:border-slate-700 focus:border-purple-600 dark:focus:border-purple-400 bg-transparent text-slate-900 dark:text-white outline-none transition-colors resize-y"></textarea>
            </div>

            <!-- Submit Action Bar -->
            <div class="pt-4 flex items-center justify-between">
                <button type="submit" id="btn-submit-gform" class="px-8 py-3 rounded-xl font-bold text-sm text-white bg-purple-600 hover:bg-purple-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirimkan Jawaban (Submit)</span>
                </button>

                <button type="reset" class="text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    Kosongkan formulir
                </button>
            </div>
        </form>

        <div class="mt-12 text-center text-[11px] text-slate-400 space-y-1">
            <p>Formulir ini dibuat dan diintegrasikan untuk Sistem LibraryCare CRM.</p>
            <p>Terhubung langsung dengan MySQL & Admin Dashboard melalui RESTful Webhook.</p>
        </div>
    </div>
</div>

<script>
    async function submitViaGFormsWebhook(e) {
        e.preventDefault();
        
        const form = document.getElementById('library-google-form');
        const submitBtn = document.getElementById('btn-submit-gform');
        const alertSuccess = document.getElementById('gform-alert-success');
        const alertError = document.getElementById('gform-alert-error');
        const alertMessage = document.getElementById('gform-alert-message');
        const errorMessage = document.getElementById('gform-error-message');
        const btnTrack = document.getElementById('btn-track-ticket');

        alertSuccess.classList.add('hidden');
        alertError.classList.add('hidden');

        // Loading state
        submitBtn.disabled = true;
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Mengirim ke Webhook...
        `;

        const categoryInput = document.querySelector('input[name="category_id"]:checked');
        const payload = {
            visitor_name: document.getElementById('gform_visitor_name').value,
            visitor_email: document.getElementById('gform_visitor_email').value,
            visitor_phone: document.getElementById('gform_visitor_phone').value,
            category_id: categoryInput ? parseInt(categoryInput.value) : null,
            title: document.getElementById('gform_title').value,
            description: document.getElementById('gform_description').value,
        };

        try {
            const response = await fetch("{{ $webhookUrl }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-API-KEY": "{{ $apiKey }}"
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                form.classList.add('hidden');
                alertSuccess.classList.remove('hidden');
                alertMessage.innerHTML = `Laporan dari Google Forms berhasil dicatat ke sistem! Nomor tiket Anda: <strong class="font-mono text-emerald-950 dark:text-emerald-100">${data.ticket_number}</strong>. Laporan langsung masuk ke antrean Admin Perpustakaan.`;
                btnTrack.href = "{{ route('complaints.track') }}?ticket=" + encodeURIComponent(data.ticket_number);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                alertError.classList.remove('hidden');
                errorMessage.textContent = data.message || "Gagal memproses pengiriman data formulir.";
            }
        } catch (err) {
            alertError.classList.remove('hidden');
            errorMessage.textContent = "Koneksi ke endpoint webhook gagal: " + err.message;
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    }

    function resetGForm() {
        document.getElementById('library-google-form').reset();
        document.getElementById('library-google-form').classList.remove('hidden');
        document.getElementById('gform-alert-success').classList.add('hidden');
        document.getElementById('gform-alert-error').classList.add('hidden');
    }
</script>
@endsection

