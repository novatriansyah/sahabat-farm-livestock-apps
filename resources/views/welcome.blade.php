@extends('layouts.guest')

@section('title', 'Manajemen Peternakan Modern')

@section('content')
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
                @php
                    $dashboardUrl = auth()->user()->role === 'MITRA' ? route('partner.dashboard') : route('dashboard');
                @endphp
                <a href="{{ $dashboardUrl }}" class="group inline-flex justify-center items-center px-10 py-5 text-xl font-bold text-white bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 rounded-2xl shadow-2xl shadow-slate-900/20 dark:shadow-emerald-900/40 transition-all hover:-translate-y-1 active:scale-95">
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
        
        <!-- Real Admin Dashboard Screenshot Preview -->
        <div class="mt-24 sm:mt-32 w-full max-w-6xl mx-auto relative px-2 sm:px-0 animate-fade-in-up" style="animation-duration: 1.3s;">
            <div class="absolute -inset-1 sm:-inset-4 bg-gradient-to-r from-emerald-500/30 to-teal-500/30 rounded-[2.5rem] sm:rounded-[4rem] blur-3xl opacity-30 dark:opacity-40"></div>
            <div class="relative bg-white dark:bg-slate-800 rounded-[2rem] sm:rounded-[3.5rem] p-1.5 sm:p-3.5 shadow-2xl border border-slate-200 dark:border-slate-700 transition-transform duration-700 hover:scale-[1.01]">
                <div class="bg-slate-100 dark:bg-slate-900 rounded-[1.2rem] sm:rounded-[2.8rem] overflow-hidden border border-slate-200/50 dark:border-slate-800 shadow-inner">
                    <img src="{{ asset('img/dashboard_preview.png') }}" alt="Admin Dashboard Live Preview" class="w-full h-auto object-cover opacity-95 group-hover:opacity-100 transition-opacity" />
                    
                    <!-- Overlay to prevent direct interaction with sample image and add depth -->
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-white/10 dark:from-slate-900/40 pointer-events-none"></div>
                </div>
            </div>
            
            <!-- Floating decorative labels -->
            <div class="absolute -top-10 -right-10 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-xl border border-emerald-100 dark:border-emerald-900 hidden lg:block animate-bounce" style="animation-duration: 3s">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-sm font-bold">Real-time Data Active</span>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Stats Section -->
<section class="py-20 bg-emerald-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="flex flex-col gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white">500+</span>
                <span class="text-emerald-100 font-bold uppercase tracking-wider text-xs">Peternak Terdaftar</span>
            </div>
            <div class="flex flex-col gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white">10K+</span>
                <span class="text-emerald-100 font-bold uppercase tracking-wider text-xs">Ternak Dikelola</span>
            </div>
            <div class="flex flex-col gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white">25+</span>
                <span class="text-emerald-100 font-bold uppercase tracking-wider text-xs">Kota Wilayah Kerja</span>
            </div>
            <div class="flex flex-col gap-2">
                <span class="text-4xl sm:text-5xl font-black text-white">99%</span>
                <span class="text-emerald-100 font-bold uppercase tracking-wider text-xs">Akurasi Data</span>
            </div>
        </div>
    </div>
</section>

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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-14">
            @php
            $features = [
                [
                    'title' => 'Profil Hewan Digital',
                    'desc' => 'Setiap ekor ternak memiliki kartu identitas digital lengkap dengan silsilah, vaksinasi, dan riwayat mutasi.',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    'link' => route('pages.digital-livestock'),
                    'color' => 'emerald'
                ],
                [
                    'title' => 'Smart Breeding Tracking',
                    'desc' => 'Jangan pernah melewatkan masa subur. Sistem kami secara otomatis menghitung masa kebuntingan dan hari perkiraan lahir.',
                    'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'link' => '#',
                    'color' => 'amber'
                ],
                [
                    'title' => 'Laporan Akuntansi Otomatis',
                    'desc' => 'Hasilkan laporan laba rugi, HPP, dan valuasi stok pakan secara instan tanpa perlu keahlian akuntansi mendalam.',
                    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'link' => route('pages.sales-tracking'),
                    'color' => 'blue'
                ],
                [
                    'title' => 'Scan QR Inovatif',
                    'desc' => 'Akses cepat data hewan hanya dengan scan QR Code. Mendukung upload dari galeri dan integrasi kamera mobile.',
                    'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
                    'link' => '#',
                    'color' => 'teal'
                ],
                [
                    'title' => 'Manajemen Pakan & Stok',
                    'desc' => 'Kelola gudang pakan dengan sistem First-In-First-Out (FIFO) dan pantau sisa stok secara real-time.',
                    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'link' => route('pages.feed-management'),
                    'color' => 'indigo'
                ],
                [
                    'title' => 'Health Monitoring',
                    'desc' => 'Catat riwayat penyakit, pemberian vitamin, dan jadwal vaksinasi untuk menjaga kesehatan seluruh koloni.',
                    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'link' => route('pages.health-monitoring'),
                    'color' => 'rose'
                ]
            ];
            @endphp

            @foreach ($features as $f)
            <div class="group p-10 sm:p-14 rounded-[3rem] bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 hover:bg-white dark:hover:bg-slate-800 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 hover:-translate-y-4">
                <a href="{{ $f['link'] }}" class="block">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-[1.8rem] sm:rounded-[2rem] bg-{{ $f['color'] }}-100/50 dark:bg-{{ $f['color'] }}-900/30 flex items-center justify-center mb-10 text-{{ $f['color'] }}-600 dark:text-{{ $f['color'] }}-400 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"></path></svg>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black mb-6 dark:text-white leading-tight group-hover:text-{{ $f['color'] }}-500 transition-colors">{{ $f['title'] }}</h3>
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">{{ $f['desc'] }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="py-24 sm:py-40 bg-slate-50 dark:bg-slate-800/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <div class="flex-1 space-y-10">
                <h2 class="text-3xl sm:text-5xl md:text-6xl font-black text-slate-900 dark:text-white leading-tight">
                    Siap Membawa <br/> <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-2xl">Revolusi Digital</span> <br/> ke Kandang Anda?
                </h2>
                <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Sahabat Farm lahir dari semangat untuk membantu peternak lokal bersaing di era digital. Kami mengombinasikan kearifan lokal peternakan dengan teknologi cloud terbaru untuk hasil maksimal.
                </p>
                <div class="space-y-6">
                    @foreach([
                        'Terintegrasi dengan sistem IoT (Dalam Pengembangan)',
                        'Data tersimpan aman di infrastruktur cloud terpercaya',
                        'Tim support ahli yang siap membantu implementasi',
                        'User interface ramah pengguna, bahkan untuk pemula'
                    ] as $item)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-lg font-bold text-slate-700 dark:text-slate-200">{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex-1 relative">
                <div class="absolute -inset-10 bg-emerald-500/10 dark:bg-emerald-500/5 blur-[100px] rounded-full"></div>
                <div class="relative rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white dark:border-slate-800 transform rotate-2">
                    <img src="{{ asset('img/logo.png') }}" class="w-full h-auto bg-white p-20 opacity-90" alt="About Sahabat Farm" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="py-24 sm:py-40 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-24">
            <h2 class="text-3xl sm:text-5xl font-black mb-6">Apa Kata Mereka?</h2>
            <p class="text-xl text-slate-500 font-medium">Testimoni nyata dari para mitra yang telah bergabung.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @for ($i = 0; $i < 3; $i++)
            <div class="p-10 rounded-[2.5rem] bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700">
                <div class="flex gap-1 text-amber-500 mb-6">
                    @for ($j = 0; $j < 5; $j++)
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-lg italic text-slate-600 dark:text-slate-300 mb-8 leading-relaxed font-medium">
                    "Setelah menggunakan Sahabat Farm, saya bisa memantau pertumbuhan kambing hanya dari handphone. Sangat membantu untuk efisiensi pakan!"
                </p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100"></div>
                    <div>
                        <h4 class="font-bold">Mitra Peternak {{ $i + 1 }}</h4>
                        <span class="text-sm text-slate-500">Owner Farm Maju Jaya</span>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-24 sm:py-40 relative overflow-hidden">
    <div class="absolute inset-0 bg-slate-900 rounded-[4rem] mx-4 sm:mx-8"></div>
    <div class="relative z-10 max-w-5xl mx-auto px-4 text-center">
        <h2 class="text-4xl sm:text-6xl font-black text-white mb-10 leading-tight">Mulai Transformasi <br/> Peternakan Anda Hari Ini</h2>
        <p class="text-xl text-slate-400 mb-14 max-w-2xl mx-auto leading-relaxed">Bergabunglah dengan ratusan peternak lainnya yang telah mendigitalisasi bisnis mereka. Gratis konsultasi awal!</p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <a href="{{ route('login') }}" class="px-10 py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xl rounded-2xl transition-all hover:-translate-y-1">Mulai Sekarang</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="px-10 py-5 bg-white text-slate-900 font-black text-xl rounded-2xl transition-all hover:-translate-y-1">Daftar Akun</a>
            @endif
        </div>
    </div>
</section>
@endsection

@push('styles')
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
@endpush
