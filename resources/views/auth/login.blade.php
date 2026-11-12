<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Portal Login Petugas</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Akses khusus Administrator & Petugas Perpustakaan</p>
    </div>

    <!-- Quick Demo Auto-fill Helper -->
    <div class="mb-5 p-3 rounded-2xl bg-teal-50 dark:bg-teal-950/50 border border-teal-200 dark:border-teal-800/80">
        <p class="text-[11px] font-bold text-teal-800 dark:text-teal-300 mb-2 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                <span>Akun Administrator Default:</span>
            </span>
            <button type="button" onclick="fillAdmin()" class="py-1 px-2.5 rounded-lg bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-[10px] font-bold hover:opacity-90 transition-opacity">
                👑 Isi Otomatis
            </button>
        </p>
        <p class="text-[10px] text-slate-500 dark:text-slate-400">
            Pengunjung/Mahasiswa tidak perlu login untuk menyampaikan laporan dan dapat langsung melacak menggunakan Nomor Tiket.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 text-xs font-semibold text-teal-600 dark:text-teal-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email Petugas</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                placeholder="admin@librarycare.id"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-rose-500 @enderror">
            @error('email')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kata Sandi</label>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-navy-950 text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-rose-500 @enderror">
            @error('password')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700 text-teal-600 focus:ring-teal-500">
                <span class="ms-2 text-xs text-slate-600 dark:text-slate-400">Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-slate-900 dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 shadow-md transition-all flex items-center justify-center gap-2">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>Masuk ke Dashboard Admin</span>
        </button>

        <div class="text-center pt-2 text-xs">
            <a href="{{ route('landing') }}" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 inline-flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Halaman Depan</span>
            </a>
        </div>
    </form>

    <script>
        function fillAdmin() {
            document.getElementById('email').value = 'admin@librarycare.id';
            document.getElementById('password').value = 'admin123';
        }
    </script>
</x-guest-layout>
