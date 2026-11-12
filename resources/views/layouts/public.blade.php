<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LibraryCare – Layanan Pengaduan & Saran Perpustakaan')</title>
    <meta name="description" content="Sistem CRM Layanan Perpustakaan untuk Pengelolaan Keluhan dan Saran Pengunjung Terintegrasi WhatsApp, Telegram, dan Google Forms.">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN for reliable standalone execution on XAMPP) -->
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
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#0d9488',
                            600: '#0f766e',
                            700: '#115e59',
                            900: '#134e4a',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- AOS (Animate on Scroll) -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Theme Initialization (Prevent theme flash) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .dark ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-navy-950 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-200">

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col space-y-3 pointer-events-none">
        @if(session('success'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-teal-500 border border-slate-200/80 dark:border-slate-800 animate-slide-in max-w-md">
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
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-rose-500 border border-slate-200/80 dark:border-slate-800 animate-slide-in max-w-md">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-semibold text-slate-900 dark:text-white">Terjadi Kendala</p>
                    <p class="text-slate-600 dark:text-slate-300">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-sky-500 border border-slate-200/80 dark:border-slate-800 animate-slide-in max-w-md">
                <i data-lucide="info" class="w-5 h-5 text-sky-600 dark:text-sky-400 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-semibold text-slate-900 dark:text-white">Informasi</p>
                    <p class="text-slate-600 dark:text-slate-300">{{ session('info') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-navy-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Brand Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-900 to-teal-700 flex items-center justify-center text-white shadow-md shadow-teal-500/10 group-hover:scale-105 transition-transform">
                        <i data-lucide="book-open" class="w-5 h-5 text-teal-300"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center">
                            Library<span class="text-teal-600 dark:text-teal-400">Care</span>
                        </span>
                        <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 -mt-1 hidden sm:block">CRM & Pengaduan Perpustakaan</p>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-7">
                    <a href="{{ route('landing') }}#alur" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                        Alur
                    </a>
                    <a href="{{ route('landing') }}#kategori" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                        Kategori
                    </a>
                    <a href="{{ route('landing') }}#kanal-pengaduan" class="text-sm font-medium text-teal-600 dark:text-teal-400 hover:underline transition-colors flex items-center gap-1 font-semibold">
                        <i data-lucide="radio" class="w-3.5 h-3.5"></i>
                        4 Saluran Pengaduan
                    </a>
                    <a href="{{ route('landing') }}#form-pengaduan" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                        Form Web
                    </a>
                    <a href="{{ route('complaints.track') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors flex items-center gap-1.5">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-teal-500"></i>
                        Lacak Laporan
                    </a>
                    <a href="{{ route('landing') }}#faq" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                        FAQ
                    </a>
                </nav>

                <!-- Right Actions: Theme Toggle & Auth -->
                <div class="flex items-center gap-3">
                    <!-- Dark/Light Theme Button -->
                    <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-colors">
                        <i id="theme-icon-sun" data-lucide="sun" class="w-5 h-5 hidden dark:block text-amber-400"></i>
                        <i id="theme-icon-moon" data-lucide="moon" class="w-5 h-5 block dark:hidden text-slate-700"></i>
                    </button>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 shadow-sm transition-all">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                <span>Dashboard Admin</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            <i data-lucide="lock" class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400"></i>
                            <span>Login Petugas</span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" type="button" class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Nav Dropdown -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-slate-200 dark:border-slate-800 space-y-2">
                <a href="{{ route('landing') }}#alur" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Alur Layanan</a>
                <a href="{{ route('landing') }}#kategori" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Kategori Keluhan</a>
                <a href="{{ route('landing') }}#form-pengaduan" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Kirim Keluhan</a>
                <a href="{{ route('complaints.track') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Lacak Tiket</a>
                <a href="{{ route('landing') }}#faq" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">FAQ</a>
                @guest
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                        <a href="{{ route('login') }}" class="block text-center py-2 text-xs font-bold rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                            Login Petugas Perpustakaan
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-navy-900 border-t border-slate-200 dark:border-slate-800/80 mt-20 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-12">
                <!-- Col 1: About -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-900 to-teal-700 flex items-center justify-center text-white">
                            <i data-lucide="book-open" class="w-4 h-4 text-teal-300"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Library<span class="text-teal-600 dark:text-teal-400">Care</span>
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-w-md">
                        Sistem Customer Relationship Management (CRM) perpustakaan modern untuk memfasilitasi aspirasi, keluhan fasilitas, dan kritik saran pengunjung secara transparan dan responsif. Terintegrasi WhatsApp Cloud API, Telegram Bot, dan Google Forms.
                    </p>
                    <div class="flex items-center gap-4 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Layanan Terbuka & Aktif
                        </span>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Navigasi Layanan</h3>
                    <ul class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                        <li><a href="{{ route('landing') }}#alur" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Alur Pengaduan</a></li>
                        <li><a href="{{ route('landing') }}#kategori" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Kategori Keluhan</a></li>
                        <li><a href="{{ route('landing') }}#form-pengaduan" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Kirim Formulir Web</a></li>
                        <li><a href="{{ route('complaints.track') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Lacak Progres Tiket</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">Portal Staf / Admin</a></li>
                    </ul>
                </div>

                <!-- Col 3: Channel Integration -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Kanal Komunikasi</h3>
                    <ul class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-center gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4 text-emerald-500"></i>
                            <span>WhatsApp Business API</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                            <span>Telegram Bot @LibraryCareBot</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-500"></i>
                            <span>Google Forms & Sheets</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-indigo-500"></i>
                            <span>library@university.ac.id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-12 pt-8 border-t border-slate-200/80 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>&copy; {{ date('Y') }} LibraryCare – Sistem CRM Layanan Perpustakaan. All rights reserved.</p>
                <p class="text-[11px]">Tugas Akhir Sistem Informasi &bull; Berbasis Laravel, Tailwind CSS & APIs</p>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true,
            disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches
        });

        // Initialize Lucide Icons
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

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>

