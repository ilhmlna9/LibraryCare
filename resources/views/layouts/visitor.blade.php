<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Pengunjung') – LibraryCare CRM</title>

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        },
                        teal: {
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Theme Initialization -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-navy-950 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-150">

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col space-y-3 pointer-events-none">
        @if(session('success'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-teal-500 border border-slate-200 dark:border-slate-800 max-w-md">
                <i data-lucide="check-circle" class="w-5 h-5 text-teal-600 dark:text-teal-400 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-semibold text-slate-900 dark:text-white">Berhasil</p>
                    <p class="text-slate-600 dark:text-slate-300">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-rose-500 border border-slate-200 dark:border-slate-800 max-w-md">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-semibold text-slate-900 dark:text-white">Kendala</p>
                    <p class="text-slate-600 dark:text-slate-300">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Top Visitor Navbar -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-navy-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('visitor.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 dark:bg-teal-600 text-white flex items-center justify-center shadow-md">
                            <i data-lucide="book-open" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">
                                Library<span class="text-teal-600 dark:text-teal-400">Care</span>
                            </span>
                            <span class="block text-[10px] text-teal-600 dark:text-teal-400 font-medium -mt-1">Portal Pengunjung</span>
                        </div>
                    </a>

                    <!-- Nav Links -->
                    <nav class="hidden md:flex items-center gap-6">
                        <a href="{{ route('visitor.dashboard') }}" class="text-sm font-semibold transition-colors {{ request()->routeIs('visitor.dashboard') ? 'text-teal-600 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('visitor.history') }}" class="text-sm font-semibold transition-colors {{ request()->routeIs('visitor.history') ? 'text-teal-600 dark:text-teal-400' : 'text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400' }}">
                            Riwayat Pengaduan
                        </a>
                        <a href="{{ route('landing') }}#form-pengaduan" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors flex items-center gap-1.5">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-teal-500"></i>
                            Buat Keluhan Baru
                        </a>
                        <a href="{{ route('complaints.track') }}" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                            Lacak Tiket
                        </a>
                    </nav>
                </div>

                <!-- Right Nav -->
                <div class="flex items-center gap-3">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i id="theme-icon-sun" data-lucide="sun" class="w-5 h-5 hidden dark:block text-amber-400"></i>
                        <i id="theme-icon-moon" data-lucide="moon" class="w-5 h-5 block dark:hidden text-slate-700"></i>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" id="visitor-dropdown-wrapper">
                        <button id="visitor-dropdown-btn" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-semibold text-slate-700 dark:text-slate-200 max-w-[120px] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>

                        <div id="visitor-dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-navy-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 text-xs">
                                <p class="font-semibold text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('visitor.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                <span>Profil & Telegram</span>
                            </a>
                            <a href="{{ route('landing') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                <i data-lucide="globe" class="w-4 h-4 text-slate-400"></i>
                                <span>Halaman Utama</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white dark:bg-navy-900 border-t border-slate-200 dark:border-slate-800 py-6 mt-12 text-center text-xs text-slate-500 transition-colors">
        &copy; {{ date('Y') }} LibraryCare Perpustakaan &bull; Customer Relationship Management
    </footer>

    <script>
        lucide.createIcons();

        // Theme Toggle Handler
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                lucide.createIcons();
            });
        }

        // Dropdown Toggle
        const dropdownBtn = document.getElementById('visitor-dropdown-btn');
        const dropdownMenu = document.getElementById('visitor-dropdown-menu');
        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', () => {
                dropdownMenu.classList.add('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>

