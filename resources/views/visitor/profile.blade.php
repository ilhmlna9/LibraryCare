@extends('layouts.visitor')

@section('title', 'Profil Pengunjung & Pengaturan Kontak')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Profil Pengunjung & Pengaturan Kontak</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola informasi diri Anda dan hubungkan saluran notifikasi WhatsApp serta Telegram</p>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white dark:bg-navy-900 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('visitor.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500 @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email (Disabled) -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Alamat Email (Akun)
                </label>
                <input type="email" id="email" value="{{ $user->email }}" disabled
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                <p class="text-[10px] text-slate-400 mt-1">Alamat email digunakan sebagai identitas akun Anda.</p>
            </div>

            <!-- Phone WhatsApp -->
            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Nomor WhatsApp Aktif
                </label>
                <div class="relative">
                    <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="081234567890"
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Notifikasi nomor tiket dan perkembangan laporan akan otomatis dikirimkan ke nomor ini.</p>
            </div>

            <!-- Telegram Chat ID with Step-by-Step Box -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Integrasi Notifikasi Telegram</h3>
                </div>

                <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-xs text-slate-700 dark:text-slate-300 space-y-2 leading-relaxed">
                    <p class="font-bold text-sky-900 dark:text-sky-300">Cara Mendapatkan Chat ID Telegram Anda:</p>
                    <ol class="list-decimal pl-4 space-y-1 text-[11px] text-slate-600 dark:text-slate-400">
                        <li>Buka aplikasi Telegram di HP atau Laptop Anda.</li>
                        <li>Cari akun bot resmi: <strong class="text-sky-600 dark:text-sky-400 font-mono">@LibraryCareBot</strong> atau buka link bot perpustakaan.</li>
                        <li>Ketik dan kirimkan perintah <strong class="text-sky-600 dark:text-sky-400 font-mono">/start</strong>.</li>
                        <li>Bot akan membalas dengan menampilkan <strong>Chat ID Angka</strong> Anda.</li>
                        <li>Salin angka tersebut dan tempelkan pada kolom di bawah ini.</li>
                    </ol>
                </div>

                <div>
                    <label for="telegram_chat_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Telegram Chat ID Anda
                    </label>
                    <input type="text" id="telegram_chat_id" name="telegram_chat_id" value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}" placeholder="Contoh: 987654321"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white font-mono outline-none focus:ring-1 focus:ring-teal-500">
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Ubah Password Akun (Opsional)</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Password Baru
                        </label>
                        <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak diubah"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500 @error('password') border-rose-500 @enderror">
                        @error('password')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-sm transition-colors flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

