@extends('layouts.guest')

@section('title', 'Tentang Kami')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Tentang <span class="text-emerald-500">Sahabat Farm</span>
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Membangun masa depan peternakan Indonesia yang modern, efisien, dan berkelanjutan.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start mb-32">
            <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <h2 class="text-3xl font-bold dark:text-white">Visi & Misi Kami</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Sahabat Farm didirikan oleh sekelompok ahli peternakan dan teknologi yang memiliki satu mimpi: mendigitalisasi sektor peternakan agar peternak lokal dapat bersaing secara global.
                </p>
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-800 shadow-xl border border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold mb-4 text-emerald-600">Visi Kami</h3>
                    <p class="text-slate-600 dark:text-slate-300">Menjadi platform manajemen peternakan nomor satu di Asia Tenggara yang memberdayakan peternak kecil hingga skala industri.</p>
                </div>
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-800 shadow-xl border border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold mb-4 text-emerald-600">Misi Kami</h3>
                    <ul class="space-y-2 text-slate-600 dark:text-slate-300">
                        <li>• Menyediakan teknologi yang mudah digunakan oleh seluruh lapisan peternak.</li>
                        <li>• Meningkatkan akurasi data untuk mengoptimalkan profitabilitas peternak.</li>
                        <li>• Memfasilitasi ekosistem peternakan yang transparan dan akuntabel.</li>
                    </ul>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="aspect-square rounded-3xl overflow-hidden shadow-lg border-4 border-white dark:border-slate-700 transform rotate-3">
                     <img src="{{ asset('img/logo.png') }}" class="w-full h-full object-cover p-10 bg-emerald-50" alt="Team 1" />
                </div>
                <div class="aspect-square rounded-3xl overflow-hidden shadow-lg border-4 border-white dark:border-slate-700 transform -rotate-3 mt-10">
                     <img src="{{ asset('img/logo.png') }}" class="w-full h-full object-cover p-10 bg-teal-50" alt="Team 2" />
                </div>
                <div class="aspect-square rounded-3xl overflow-hidden shadow-lg border-4 border-white dark:border-slate-700 transform -rotate-2">
                     <img src="{{ asset('img/logo.png') }}" class="w-full h-full object-cover p-10 bg-blue-50" alt="Team 3" />
                </div>
                <div class="aspect-square rounded-3xl overflow-hidden shadow-lg border-4 border-white dark:border-slate-700 transform rotate-2 mt-4">
                     <img src="{{ asset('img/logo.png') }}" class="w-full h-full object-cover p-10 bg-slate-50" alt="Team 4" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
