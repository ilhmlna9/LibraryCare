<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Daftar Akun Pengunjung</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Buat akun untuk memantau seluruh riwayat pengaduan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                placeholder="Contoh: Budi Pratama"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-rose-500 @enderror">
            @error('name')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                placeholder="nama@student.ac.id"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-rose-500 @enderror">
            @error('email')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone WhatsApp -->
        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nomor WhatsApp</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                placeholder="081234567890"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-rose-500 @enderror">
            @error('phone')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-rose-500 @enderror">
            @error('password')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Konfirmasi Kata Sandi <span class="text-rose-500">*</span></label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Ulangi kata sandi"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 transition-all flex items-center justify-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Daftar Akun Pengunjung</span>
        </button>

        <div class="text-center pt-2 text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-teal-600 dark:text-teal-400 hover:underline">
                Masuk di Sini
            </a>
        </div>
    </form>
</x-guest-layout>
