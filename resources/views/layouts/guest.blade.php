<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LibraryCare') }} – Masuk & Registrasi</title>

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
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-navy-950 dark:text-slate-100 min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 transition-colors duration-150 relative">

    <!-- Theme Toggle at top right -->
    <div class="absolute top-4 right-4">
        <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
            <i id="theme-icon-sun" data-lucide="sun" class="w-5 h-5 hidden dark:block text-amber-400"></i>
            <i id="theme-icon-moon" data-lucide="moon" class="w-5 h-5 block dark:hidden text-slate-700"></i>
        </button>
    </div>

    <div class="w-full max-w-md">
        <!-- Brand Logo Header -->
        <div class="text-center mb-6">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-900 to-teal-700 flex items-center justify-center text-white shadow-lg shadow-teal-500/20 group-hover:scale-105 transition-transform">
                    <i data-lucide="book-open" class="w-6 h-6 text-teal-300"></i>
                </div>
                <div class="text-left">
                    <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        Library<span class="text-teal-600 dark:text-teal-400">Care</span>
                    </span>
                    <p class="text-xs text-slate-500 dark:text-slate-400 -mt-1 font-medium">Sistem CRM Layanan Perpustakaan</p>
                </div>
            </a>
        </div>

        <!-- Auth Card Container -->
        <div class="bg-white dark:bg-navy-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none">
            {{ $slot }}
        </div>

        <p class="text-center text-xs text-slate-500 dark:text-slate-500 mt-6">
            &larr; <a href="{{ route('landing') }}" class="hover:underline text-teal-600 dark:text-teal-400 font-medium">Kembali ke Halaman Beranda</a>
        </p>
    </div>

    <script>
        lucide.createIcons();

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
    </script>
</body>
</html>
