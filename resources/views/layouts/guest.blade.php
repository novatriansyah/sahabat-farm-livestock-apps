<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Sahabat Farm')) - Manajemen Peternakan Modern</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/logo.png') }}">

    @stack('styles')
</head>
<body class="antialiased font-sans bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100 overflow-x-hidden selection:bg-emerald-500 selection:text-white">

    <div x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" class="relative min-h-screen flex flex-col">
        
        <!-- Navbar -->
        <nav 
            :class="scrolled ? 'bg-white/90 dark:bg-slate-900/90 backdrop-blur-md shadow-lg border-b py-2' : 'bg-transparent border-transparent py-4 sm:py-6'"
            class="fixed w-full z-50 transition-all duration-300 border-slate-200/50 dark:border-slate-800/50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-12">
                    <!-- Logo -->
                    <a href="/" class="flex-shrink-0 flex items-center gap-2 sm:gap-3 group">
                        <img src="{{ asset('img/logo.png') }}" class="h-8 w-8 sm:h-10 sm:w-10 object-contain transition-transform duration-300 group-hover:scale-110" alt="Sahabat Farm Logo" />
                        <span class="font-extrabold text-lg sm:text-2xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">
                            Sahabat Farm
                        </span>
                    </a>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-10">
                        <a href="{{ url('/#features') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Fitur</a>
                        <a href="{{ url('/#about') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Tentang</a>
                        <a href="{{ url('/#testimonials') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Ulasan</a>
                        
                        <div class="flex items-center gap-4 ml-6 pl-6 border-l border-slate-200 dark:border-slate-700">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-md shadow-emerald-600/20 transition-all active:scale-95">
                                    Dasbor
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors px-2">
                                    Masuk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 rounded-xl shadow-md transition-all active:scale-95">
                                        Mulai
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-slate-600 dark:text-slate-300 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none">
                            <span class="sr-only">Menu</span>
                            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div 
                x-show="mobileMenuOpen" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4"
                class="md:hidden absolute top-full left-0 w-full bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden"
            >
                <div class="px-4 py-8 space-y-3">
                    <a href="{{ url('/#features') }}" @click="mobileMenuOpen = false" class="block px-4 py-4 text-lg font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-2xl">Fitur</a>
                    <a href="{{ url('/#about') }}" @click="mobileMenuOpen = false" class="block px-4 py-4 text-lg font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-2xl">Tentang</a>
                    <a href="{{ url('/#testimonials') }}" @click="mobileMenuOpen = false" class="block px-4 py-4 text-lg font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-2xl">Ulasan</a>
                    <div class="py-4 px-4"><div class="h-px bg-slate-100 dark:bg-slate-700"></div></div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block px-4 py-5 text-center text-lg font-extrabold text-white bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-500/30">
                            Buka Dasbor
                        </a>
                    @else
                        <div class="grid grid-cols-1 {{ Route::has('register') ? 'sm:grid-cols-2' : '' }} gap-3">
                            <a href="{{ route('login') }}" class="block px-4 py-4 text-center text-lg font-bold text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-700/50 rounded-2xl">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block px-4 py-4 text-center text-lg font-extrabold text-white bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-500/30">Daftar Akun</a>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-24 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 sm:gap-16 mb-20 bg-white dark:bg-slate-800 p-12 sm:p-20 rounded-[3rem] sm:rounded-[4rem] border border-slate-200 dark:border-slate-700/50 shadow-sm relative overflow-hidden transition-all hover:shadow-xl group">
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-4 mb-8">
                            <img src="{{ asset('img/logo.png') }}" class="h-10 w-10 object-contain" alt="Sahabat Farm Logo" />
                            <span class="font-black text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight">Sahabat Farm</span>
                        </div>
                        <p class="text-lg text-slate-500 dark:text-slate-400 font-medium max-w-sm mb-10 leading-relaxed">
                            Membangun ekosistem peternakan yang berkelanjutan dan modern melalui inovasi teknologi yang merakyat.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-bold mb-8 dark:text-white">Layanan</h4>
                        <ul class="space-y-4 text-slate-500 dark:text-slate-400 font-medium">
                            <li><a href="{{ route('pages.digital-livestock') }}" class="hover:text-emerald-500 transition-colors">Digitalisasi Ternak</a></li>
                            <li><a href="{{ route('pages.health-monitoring') }}" class="hover:text-emerald-500 transition-colors">Monitoring Kesehatan</a></li>
                            <li><a href="{{ route('pages.feed-management') }}" class="hover:text-emerald-500 transition-colors">Manajemen Pakan</a></li>
                            <li><a href="{{ route('pages.sales-tracking') }}" class="hover:text-emerald-500 transition-colors">Sales Tracking</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-bold mb-8 dark:text-white">Perusahaan</h4>
                        <ul class="space-y-4 text-slate-500 dark:text-slate-400 font-medium">
                            <li><a href="{{ route('pages.about-us') }}" class="hover:text-emerald-500 transition-colors">Tentang Kami</a></li>
                            <li><a href="{{ route('pages.terms') }}" class="hover:text-emerald-500 transition-colors">Syarat & Ketentuan</a></li>
                            <li><a href="{{ route('pages.privacy') }}" class="hover:text-emerald-500 transition-colors">Kebijakan Privasi</a></li>
                            <li><a href="{{ route('pages.contact') }}" class="hover:text-emerald-500 transition-colors">Hubungi Kami</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="text-center">
                    <p class="text-slate-400 dark:text-slate-500 text-sm font-semibold tracking-wide">
                        &copy; {{ date('Y') }} SAHABAT FARM INDONESIA. SEMUA HAK DILINDUNGI.
                    </p>
                </div>
            </div>
        </footer>

        <!-- Custom Transitions & Scroll Behaviors -->
        <style>
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(40px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) both;
            }
        </style>

        @stack('scripts')
    </div>
</body>
</html>
