@extends('layouts.guest')

@section('title', 'Digitalisasi Ternak')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Revolusi <span class="text-emerald-500">Digital</span> Peternakan
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Transformasi setiap ekor ternak menjadi data berharga untuk pengambilan keputusan yang lebih tepat.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-32">
            <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <h2 class="text-3xl font-bold dark:text-white">Mengapa Digitalisasi?</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Dalam metode konvensional, riwayat ternak seringkali hilang atau tidak tercatat dengan baik. Dengan sistem digital Sahabat Farm, setiap ternak memiliki "KTP Digital" yang mencatat segalanya mulai dari lahir hingga panen.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center text-emerald-600 shrink-0">✓</div>
                        <span class="font-bold">Pelacakan silsilah (Genealogy) 100% akurat</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center text-emerald-600 shrink-0">✓</div>
                        <span class="font-bold">Riwayat medis yang terpusat dan mudah diakses</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center text-emerald-600 shrink-0">✓</div>
                        <span class="font-bold">Analisis performa individu (ADG & Konversi Pakan)</span>
                    </li>
                </ul>
            </div>
            <div class="relative animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="absolute -inset-4 bg-emerald-500/20 blur-3xl rounded-full"></div>
                <div class="relative bg-white dark:bg-slate-800 p-8 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700">
                    <img src="{{ asset('img/smart_qr.png') }}" class="w-full h-auto opacity-100 grayscale" alt="Digitalization Illustration" />
                    <!-- <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-emerald-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <span class="text-xl font-black dark:text-white">Smart Tag Ready</span>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-12 sm:p-24 text-center">
            <h2 class="text-3xl sm:text-5xl font-black text-white mb-8 leading-tight">Mulai Digitalisasi Sekarang</h2>
            <p class="text-xl text-slate-400 mb-12 max-w-2xl mx-auto">Tim kami siap membantu proses on-boarding dan migrasi data peternakan Anda.</p>
            <a href="{{ route('pages.contact') }}" class="inline-flex px-10 py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xl rounded-2xl transition-all hover:-translate-y-1 shadow-lg shadow-emerald-600/20">Hubungi Konsultan Kami</a>
        </div>
    </div>
</div>
@endsection
