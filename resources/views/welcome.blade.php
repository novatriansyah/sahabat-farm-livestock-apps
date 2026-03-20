<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sahabat Farm') }} - Manajemen Peternakan Modern</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100 overflow-x-hidden selection:bg-emerald-500 selection:text-white">

    <div x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" class="relative min-h-screen">
        
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
                        <a href="#features" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Fitur</a>
                        <a href="#about" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Tentang</a>
                        
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
                    <a href="#features" @click="mobileMenuOpen = false" class="block px-4 py-4 text-lg font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-2xl">Fitur</a>
                    <a href="#about" @click="mobileMenuOpen = false" class="block px-4 py-4 text-lg font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-2xl">Tentang</a>
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

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 md:pt-56 md:pb-40 overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute top-[-20%] left-[-10%] w-[80%] h-[80%] rounded-full bg-emerald-400/10 dark:bg-emerald-500/5 blur-[120px] animate-pulse" style="animation-duration: 8s"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[80%] h-[80%] rounded-full bg-teal-400/10 dark:bg-teal-500/5 blur-[120px] animate-pulse" style="animation-duration: 10s"></div>
                <!-- Grid pattern -->
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDIwaDQwTTIwIDB2NDAiIHN0cm9rZT0icmdiYSgwLDAsMCwwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+Cjwvc3ZnPg==')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDIwaDQwTTIwIDB2NDAiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjAyKSIgc3Ryb2tlLXdpZHRoPSIxIi8+Cjwvc3ZnPg==')] [mask-image:radial-gradient(ellipse_at_center,black,transparent)]"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm text-emerald-600 dark:text-emerald-400 text-xs sm:text-sm font-bold mb-10 border border-emerald-100 dark:border-emerald-800/50 shadow-sm animate-fade-in-up" style="animation-duration: 0.5s;">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    The Future of Livestock Management
                </div>
                
                <h1 class="text-4xl sm:text-6xl md:text-8xl font-black tracking-tight mb-8 animate-fade-in-up leading-[1.1] max-w-5xl" style="animation-duration: 0.7s;">
                    Kelola Peternakan <br class="hidden sm:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600">Jauh Lebih Cerdas</span>
                </h1>
                
                <p class="text-base sm:text-xl md:text-2xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto mb-14 px-4 animate-fade-in-up leading-relaxed" style="animation-duration: 0.9s;">
                    Modernisasi operasional peternakan Anda dengan platform data-driven terlengkap. Mulai dari pemantauan kesehatan hingga analitik finansial.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-5 justify-center w-full px-4 animate-fade-in-up" style="animation-duration: 1.1s;">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="group inline-flex justify-center items-center px-10 py-5 text-xl font-bold text-white bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 rounded-2xl shadow-2xl shadow-slate-900/20 dark:shadow-emerald-900/40 transition-all hover:-translate-y-1 active:scale-95">
                            Buka Dasbor
                            <svg class="ml-3 w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="group inline-flex justify-center items-center px-10 py-5 text-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:shadow-emerald-500/40 rounded-2xl shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 active:scale-95">
                            Mulai Sekarang
                            <svg class="ml-3 w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="#features" class="inline-flex justify-center items-center px-10 py-5 text-xl font-bold text-slate-700 dark:text-white bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-2xl border-2 border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:-translate-y-1 active:scale-95">
                            Pelajari Fitur
                        </a>
                    @endauth
                </div>
                
                <!-- Premium Dashboard Preview Mockup -->
                <div class="mt-24 sm:mt-32 w-full max-w-6xl mx-auto relative px-2 sm:px-0 animate-fade-in-up" style="animation-duration: 1.3s;">
                    <div class="absolute -inset-1 sm:-inset-4 bg-gradient-to-r from-emerald-500/30 to-teal-500/30 rounded-[2.5rem] sm:rounded-[4rem] blur-3xl opacity-30 dark:opacity-40"></div>
                    <div class="relative bg-white dark:bg-slate-800 rounded-[2rem] sm:rounded-[3rem] p-2 sm:p-4 shadow-[0_32px_120px_rgba(0,0,0,0.1)] dark:shadow-[0_32px_120px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700">
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-[1.5rem] sm:rounded-[2.5rem] overflow-hidden border border-slate-100 dark:border-slate-800 shadow-inner">
                            <!-- Visual Content Wrapper to ensure responsive ratio -->
                            <div class="w-full relative aspect-[16/10] sm:aspect-video overflow-hidden group">
                                <div class="flex h-full">
                                    <!-- Sidebar Mock -->
                                    <div class="hidden sm:flex flex-col w-20 md:w-28 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 py-10 items-center gap-8">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-50 dark:from-emerald-900/40 dark:to-teal-900/20 shadow-sm"></div>
                                        @for ($i = 0; $i < 5; $i++)
                                            <div class="w-10 h-2.5 bg-slate-100 dark:bg-slate-700/50 rounded-full"></div>
                                        @endfor
                                    </div>
                                    
                                    <div class="flex-1 p-5 sm:p-12 flex flex-col gap-8">
                                        <!-- Header Mock -->
                                        <div class="flex justify-between items-end mb-4">
                                            <div class="flex flex-col gap-2">
                                                <div class="h-4 w-32 bg-slate-200 dark:bg-slate-700/50 rounded-full"></div>
                                                <div class="h-8 w-48 sm:w-80 bg-slate-300 dark:bg-slate-600 rounded-xl"></div>
                                            </div>
                                            <div class="h-12 w-12 rounded-2xl bg-slate-200 dark:bg-slate-700"></div>
                                        </div>
                                        
                                        <!-- Real Mock Stats -->
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-8">
                                            @php
                                            $statsColors = ['emerald', 'teal', 'blue', 'indigo'];
                                            @endphp
                                            @foreach ($statsColors as $color)
                                                <div class="p-4 sm:p-7 rounded-3xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm group/card hover:shadow-lg transition-all duration-300">
                                                    <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-2xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 mb-5 flex items-center justify-center text-{{ $color }}-500">
                                                        <div class="w-5 h-5 sm:w-7 sm:h-7 bg-current rounded-lg opacity-40"></div>
                                                    </div>
                                                    <div class="h-2 w-16 bg-slate-100 dark:bg-slate-700 rounded-full mb-3"></div>
                                                    <div class="h-5 w-24 sm:w-32 bg-slate-200 dark:bg-slate-600 rounded-lg"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Large Dynamic Content Area -->
                                        <div class="flex-1 rounded-3xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-6 sm:p-10 shadow-sm flex flex-col relative overflow-hidden">
                                            <div class="flex justify-between mb-10">
                                                <div class="h-5 w-48 bg-slate-100 dark:bg-slate-700 rounded-lg"></div>
                                                <div class="flex gap-2">
                                                    <div class="h-8 w-20 bg-slate-50 dark:bg-slate-700 rounded-lg"></div>
                                                    <div class="h-8 w-20 bg-slate-50 dark:bg-slate-700 rounded-lg"></div>
                                                </div>
                                            </div>
                                            <div class="flex-1 border-b-2 border-l-2 border-slate-50 dark:border-slate-700/50 relative">
                                                <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                                    <path d="M0,80 C15,30 35,70 55,40 S85,10 100,50 L100,100 L0,100 Z" fill="url(#hero-grad-large)" opacity="0.1"/>
                                                    <path d="M0,80 C15,30 35,70 55,40 S85,10 100,50" fill="none" stroke="url(#line-grad)" stroke-width="3" stroke-linecap="round"/>
                                                    <defs>
                                                        <linearGradient id="hero-grad-large" x1="0%" y1="0%" x2="0%" y2="100%">
                                                            <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                                            <stop offset="100%" style="stop-color:#10b981;stop-opacity:0" />
                                                        </linearGradient>
                                                        <linearGradient id="line-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                                            <stop offset="0%" style="stop-color:#10b981" />
                                                            <stop offset="100%" style="stop-color:#0d9488" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                                <!-- Focal Point Tooltip Mock -->
                                                <div class="absolute h-4 w-4 rounded-full bg-emerald-500 border-4 border-white dark:border-slate-800 shadow-2xl" style="left: 65%; top: 25%; transform: translate(-50%, -50%);">
                                                    <div class="absolute top-[-60px] left-1/2 -translate-x-1/2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] font-bold py-2 px-3 rounded-xl shadow-xl whitespace-nowrap">
                                                        Growth: +12.5%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="py-24 sm:py-40 bg-white dark:bg-slate-900 relative overflow-hidden">
            <!-- Subtle background decorative elements -->
            <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-emerald-50/50 dark:bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-20 sm:mb-32">
                    <h2 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight">
                        Powerfull fitur untuk <br class="hidden sm:block" /> mengelola bisnis Anda
                    </h2>
                    <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium">
                        Didesain dari nol untuk kebutuhan spesifik peternak di Indonesia.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-14">
                    <!-- Feature 1 -->
                    @php
                    $features = [
                        [
                            'title' => 'Profil Hewan Digital',
                            'desc' => 'Setiap ekor ternak memiliki kartu identitas digital lengkap dengan silsilah, vaksinasi, dan riwayat mutasi.',
                            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                            'color' => 'emerald'
                        ],
                        [
                            'title' => 'Smart Breeding Tracking',
                            'desc' => 'Jangan pernah melewatkan masa subur. Sistem kami secara otomatis menghitung masa kebuntingan dan hari perkiraan lahir.',
                            'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                            'color' => 'amber'
                        ],
                        [
                            'title' => 'Laporan Akuntansi Otomatis',
                            'desc' => 'Hasilkan laporan laba rugi, HPP, dan valuasi stok pakan secara instan tanpa perlu keahlian akuntansi mendalam.',
                            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                            'color' => 'blue'
                        ]
                    ];
                    @endphp

                    @foreach ($features as $f)
                    <div class="group p-10 sm:p-14 rounded-[3rem] bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 hover:bg-white dark:hover:bg-slate-800 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 hover:-translate-y-4">
                        <div class="w-20 h-20 rounded-[2rem] bg-{{ $f['color'] }}-100/50 dark:bg-{{ $f['color'] }}-900/30 flex items-center justify-center mb-10 text-{{ $f['color'] }}-600 dark:text-{{ $f['color'] }}-400 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"></path></svg>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black mb-6 dark:text-white leading-tight">{{ $f['title'] }}</h3>
                        <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">{{ $f['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

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
                        <div class="flex gap-4">
                            @foreach(['facebook', 'twitter', 'instagram', 'youtube'] as $social)
                            <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center cursor-pointer hover:bg-emerald-500 hover:text-white transition-all">
                                <span class="sr-only">{{ ucfirst($social) }}</span>
                                <div class="w-5 h-5 bg-current rounded-sm opacity-50"></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-bold mb-8 dark:text-white">Layanan</h4>
                        <ul class="space-y-4 text-slate-500 dark:text-slate-400 font-medium">
                            <li><a href="#" class="hover:text-emerald-500 transition-colors">Digitalisasi Ternak</a></li>
                            <li><a href="#" class="hover:text-emerald-500 transition-colors">Monitoring Kesehatan</a></li>
                            <li><a href="#" class="hover:text-emerald-500 transition-colors">Manajemen Pakan</a></li>
                            <li><a href="#" class="hover:text-emerald-500 transition-colors">Sales Tracking</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-bold mb-8 dark:text-white">Perusahaan</h4>
                        <ul class="space-y-4 text-slate-500 dark:text-slate-400 font-medium">
                            <li><a href="#" class="hover:text-emerald-500 transition-colors">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-emerald-600 transition-colors">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="hover:text-emerald-600 transition-colors">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-emerald-500 transition-colors">Hubungi Kami</a></li>
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
            @media (max-width: 640px) {
                h1 { line-height: 1.2 !important; }
            }
        </style>
    </div>
</body>
</html>
