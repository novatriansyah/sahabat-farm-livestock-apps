<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sahabat Farm') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100 overflow-x-hidden selection:bg-emerald-500 selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 backdrop-blur-md bg-white/70 dark:bg-slate-900/70 border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" class="h-8 w-8 object-contain" alt="Sahabat Farm Logo" />
                    <span class="font-bold text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">
                        Sahabat Farm Indonesia
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Fitur</a>
                    <a href="#about" class="text-sm font-medium text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">Tentang</a>
                    
                    <div class="flex items-center gap-4 ml-4 pl-4 border-l border-slate-200 dark:border-slate-700">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">
                                    Dasbor
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors">
                                    Masuk
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center pt-20 pb-12 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-emerald-500/20 dark:bg-emerald-500/10 blur-[120px] mix-blend-multiply dark:mix-blend-screen"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-teal-500/20 dark:bg-teal-500/10 blur-[120px] mix-blend-multiply dark:mix-blend-screen"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz4KPHBhdGggZD0iTTAgMjBIMDBNMjAgMHY0MCIgc3Ryb2tlPSJyZ2JhKDE1NiwgMTYzLCAxNzUsIDAuMSkiIHN0cm9rZS13aWR0aD0iMSIvPgo8L3N2Zz4=')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz4KPHBhdGggZD0iTTAgMjBIMDBNMjAgMHY0MCIgc3Ryb2tlPSJyZ2JhKDI1NSLCAyNTUsIDI1NSwgMC4wNSkiIHN0cm9rZS13aWR0aD0iMSIvPgo8L3N2Zz4=')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-sm font-medium mb-8 border border-emerald-100 dark:border-emerald-800/50 shadow-sm animate-fade-in-up" style="animation-duration: 0.5s;">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Manajemen Peternakan Modern
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 animate-fade-in-up" style="animation-duration: 0.7s;">
                Kelola peternakan Anda dengan <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">Presisi & Perhatian</span>
            </h1>
            
            <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 animate-fade-in-up" style="animation-duration: 0.9s;">
                Aplikasi Sahabat Farm menyederhanakan operasional Anda, memberikan pelacakan real-time, manajemen perkembangbiakan, dan pelaporan yang komprehensif.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto animate-fade-in-up" style="animation-duration: 1.1s;">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex justify-center items-center px-8 py-3.5 text-base font-semibold text-white bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 rounded-xl shadow-lg shadow-slate-900/20 dark:shadow-white/10 transition-all hover:-translate-y-0.5">
                        Buka Dasbor
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-8 py-3.5 text-base font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5">
                        Masuk ke Sistem
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </a>
                @endauth
            </div>
            
            <!-- Dashboard Preview Image -->
            <div class="mt-20 w-full max-w-5xl mx-auto relative animate-fade-in-up" style="animation-duration: 1.3s;">
                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-20 dark:opacity-30"></div>
                <div class="relative rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl border border-white/20 dark:border-slate-700/50 p-2 shadow-2xl">
                    <div class="rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 aspect-video flex flex-col">
                        <!-- Mock Browser Chrome -->
                        <div class="h-8 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center px-4 gap-2">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            </div>
                        </div>
                        <!-- Mock Content -->
                        <div class="flex-1 p-6 flex flex-col gap-6">
                            <!-- Stats Row -->
                            <div class="grid grid-cols-4 gap-4">
                                @for ($i = 0; $i < 4; $i++)
                                    <div class="h-24 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4 flex flex-col justify-between shadow-sm">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50"></div>
                                        <div>
                                            <div class="h-2 w-12 bg-slate-200 dark:bg-slate-700 rounded mb-2"></div>
                                            <div class="h-4 w-20 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            
                            <!-- Main Area -->
                            <div class="flex-1 grid grid-cols-3 gap-6">
                                <div class="col-span-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4 flex flex-col shadow-sm">
                                    <div class="h-4 w-32 bg-slate-200 dark:bg-slate-700 rounded mb-6"></div>
                                    <div class="flex-1 border-b border-l border-slate-100 dark:border-slate-700/50 relative">
                                        <!-- Mock Chart Lines -->
                                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDIwSDAwIiBzdHJva2U9InJnYmEoMTU2LCAxNjMsIDE3NSwgMC4yKSIgc3Ryb2tlLXdpZHRoPSIxIi8+Cjwvc3ZnPg==')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDIwSDAwIiBzdHJva2U9InJnYmEoMTQ4LCAxNjMsIDE4NCwgMC4yKSIgc3Ryb2tlLXdpZHRoPSIxIi8+Cjwvc3ZnPg==')]"></div>
                                        <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                            <path d="M0,80 Q25,20 50,50 T100,30 L100,100 L0,100 Z" fill="url(#grad)" opacity="0.2"/>
                                            <path d="M0,80 Q25,20 50,50 T100,30" fill="none" stroke="#10b981" stroke-width="2"/>
                                            <defs>
                                                <linearGradient id="grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                                    <stop offset="100%" style="stop-color:#10b981;stop-opacity:0" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4 flex flex-col shadow-sm">
                                    <div class="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded mb-6"></div>
                                    <div class="flex flex-col gap-3">
                                        @for ($i = 0; $i < 4; $i++)
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700"></div>
                                                <div class="flex-1">
                                                    <div class="h-2 w-full bg-slate-200 dark:bg-slate-700 rounded mb-1"></div>
                                                    <div class="h-2 w-1/2 bg-slate-100 dark:bg-slate-800 rounded"></div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl text-slate-900 dark:text-white mb-4">
                    Semua yang Anda butuhkan untuk menjalankan peternakan
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400">
                    Fitur andalan yang dirancang khusus untuk manajemen peternakan modern, dari kelahiran hingga penjualan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center mb-6 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Profil Hewan</h3>
                    <p class="text-slate-600 dark:text-slate-400">Catatan terperinci untuk setiap hewan termasuk detail kelahiran, genetika, riwayat kesehatan, dan pelacakan berat badan.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="w-14 h-14 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center mb-6 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Manajemen Perkembangbiakan</h3>
                    <p class="text-slate-600 dark:text-slate-400">Lacak siklus perkawinan, konfirmasi kebuntingan, dan proyeksi tanggal kelahiran dengan pelaporan terintegrasi.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center mb-6 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Laporan Komprehensif</h3>
                    <p class="text-slate-600 dark:text-slate-400">Hasilkan wawasan tentang penjualan, inventaris stok, metrik operasional, dan performa hewan untuk mendorong pertumbuhan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm p-6 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                <div class="flex items-center gap-3 mb-4 md:mb-0">
                    <img src="{{ asset('img/logo.png') }}" class="h-6 w-6 object-contain" alt="Sahabat Farm Logo" />
                    <span class="font-bold text-lg text-slate-900 dark:text-white">Sahabat Farm Indonesia</span>
                </div>
                
                <p class="text-slate-500 dark:text-slate-400 text-sm text-center">
                    &copy; {{ date('Y') }} Sahabat Farm. Hak Cipta Dilindungi Undang-Undang.
                </p>
            </div>
        </div>
    </footer>

    <!-- Animations CSS -->
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation-name: fadeInUp;
            animation-fill-mode: both;
            animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</body>
</html>
