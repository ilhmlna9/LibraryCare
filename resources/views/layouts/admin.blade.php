<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') – LibraryCare CRM</title>

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
                            700: '#334155',
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        .sidebar-transition { transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800 dark:bg-navy-950 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-150">

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col space-y-3 pointer-events-none">
        @if(session('success'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-teal-500 border border-slate-200 dark:border-slate-800 max-w-md">
                <i data-lucide="check-circle" class="w-5 h-5 text-teal-600 dark:text-teal-400 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-semibold text-slate-900 dark:text-white">Sukses</p>
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
                    <p class="font-semibold text-slate-900 dark:text-white">Kesalahan</p>
                    <p class="text-slate-600 dark:text-slate-300">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl bg-white dark:bg-navy-900 border-l-4 border-sky-500 border border-slate-200 dark:border-slate-800 max-w-md">
                <i data-lucide="info" class="w-5 h-5 text-sky-600 dark:text-sky-400 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-semibold text-slate-900 dark:text-white">Info</p>
                    <p class="text-slate-600 dark:text-slate-300">{{ session('info') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-auto">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    <div class="flex flex-1 min-h-screen">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="sidebar-transition w-64 bg-slate-900 dark:bg-navy-900 text-slate-300 flex flex-col shrink-0 border-r border-slate-800 z-30 fixed lg:static inset-y-0 left-0 -translate-x-full lg:translate-x-0">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0 shadow-md">
                        <i data-lucide="book-open" class="w-5 h-5"></i>
                    </div>
                    <div class="sidebar-text truncate">
                        <span class="font-bold text-white tracking-tight text-base">Library<span class="text-teal-400">Care</span></span>
                        <p class="text-[10px] text-teal-400/90 font-medium -mt-1">Admin CRM Panel</p>
                    </div>
                </a>
                <button id="close-mobile-sidebar" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                <div class="sidebar-text px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    Menu Utama
                </div>

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800/80 text-slate-300 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <a href="{{ route('admin.complaints.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.complaints.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800/80 text-slate-300 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="inbox" class="w-5 h-5 shrink-0"></i>
                        <span class="sidebar-text">Keluhan & Saran</span>
                    </div>
                    @php $pendingCount = \App\Models\Complaint::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="sidebar-text px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-500 text-slate-950">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800/80 text-slate-300 hover:text-white' }}">
                    <i data-lucide="tags" class="w-5 h-5 shrink-0"></i>
                    <span class="sidebar-text">Kategori Keluhan</span>
                </a>

                <a href="{{ route('admin.visitors.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.visitors.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800/80 text-slate-300 hover:text-white' }}">
                    <i data-lucide="users" class="w-5 h-5 shrink-0"></i>
                    <span class="sidebar-text">Data Pengunjung</span>
                </a>

                <div class="sidebar-text px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider pt-4 mb-2">
                    Konfigurasi & Saluran
                </div>

                <a href="{{ route('admin.integrations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.integrations.*') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800/80 text-slate-300 hover:text-white' }}">
                    <i data-lucide="radio" class="w-5 h-5 shrink-0 text-teal-400"></i>
                    <span class="sidebar-text">Integrasi API</span>
                </a>

                <a href="{{ route('landing') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800/80 hover:text-slate-200 transition-colors">
                    <i data-lucide="external-link" class="w-5 h-5 shrink-0"></i>
                    <span class="sidebar-text">Buka Website Utama</span>
                </a>
            </nav>

            <!-- Sidebar User Profile Footer -->
            <div class="p-3 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center gap-3 px-2 py-1.5">
                    <div class="w-8 h-8 rounded-full bg-teal-700 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-text truncate text-xs">
                        <p class="font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-slate-400 text-[11px] truncate">Admin Perpustakaan</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-20 hidden lg:hidden"></div>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-navy-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0 transition-colors">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button id="open-mobile-sidebar" class="lg:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <!-- Desktop Sidebar Collapse Toggle -->
                    <button id="desktop-sidebar-toggle" title="Kecilkan/Perbesar Sidebar" class="hidden lg:flex p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="panel-left-close" class="w-5 h-5"></i>
                    </button>

                    <h1 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white truncate">
                        @yield('header_title', 'Dashboard Perpustakaan')
                    </h1>
                </div>

                <!-- Top Right Items -->
                <div class="flex items-center gap-3">
                    <!-- Simulation status pill -->
                    <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sistem Aktif
                    </span>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i id="theme-icon-sun" data-lucide="sun" class="w-5 h-5 hidden dark:block text-amber-400"></i>
                        <i id="theme-icon-moon" data-lucide="moon" class="w-5 h-5 block dark:hidden text-slate-700"></i>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" id="profile-dropdown-wrapper">
                        <button id="profile-dropdown-btn" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-slate-900 dark:bg-teal-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-semibold text-slate-700 dark:text-slate-200 max-w-[120px] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>

                        <div id="profile-dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-navy-900 rounded-xl shadow-xl border border-slate-200 dark:border-slate-800 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 text-xs">
                                <p class="font-semibold text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                                <i data-lucide="user-cog" class="w-4 h-4 text-slate-400"></i>
                                <span>Profil Akun</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

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

        // Mobile Sidebar Controls
        const openMobileBtn = document.getElementById('open-mobile-sidebar');
        const closeMobileBtn = document.getElementById('close-mobile-sidebar');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleMobileSidebar(show) {
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        if (openMobileBtn) openMobileBtn.addEventListener('click', () => toggleMobileSidebar(true));
        if (closeMobileBtn) closeMobileBtn.addEventListener('click', () => toggleMobileSidebar(false));
        if (overlay) overlay.addEventListener('click', () => toggleMobileSidebar(false));

        // Desktop Sidebar Collapse Toggle with Persistence
        const desktopToggle = document.getElementById('desktop-sidebar-toggle');
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';

        function setSidebarCollapsed(collapsed) {
            const texts = document.querySelectorAll('.sidebar-text');
            if (collapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                texts.forEach(el => el.classList.add('hidden'));
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                texts.forEach(el => el.classList.remove('hidden'));
            }
            localStorage.setItem('sidebar_collapsed', collapsed ? 'true' : 'false');
            lucide.createIcons();
        }

        if (isCollapsed) {
            setSidebarCollapsed(true);
        }

        if (desktopToggle) {
            desktopToggle.addEventListener('click', () => {
                const currentlyCollapsed = sidebar.classList.contains('w-20');
                setSidebarCollapsed(!currentlyCollapsed);
            });
        }

        // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profile-dropdown-btn');
        const profileMenu = document.getElementById('profile-dropdown-menu');

        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                profileMenu.classList.add('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>

