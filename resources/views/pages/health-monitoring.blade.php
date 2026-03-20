@extends('layouts.guest')

@section('title', 'Monitoring Kesehatan')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Pantau <span class="text-rose-500">Kesehatan</span> Ternak 24/7
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Sistem peringatan dini untuk mencegah penyebaran penyakit dan menjaga produktivitas koloni.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-32">
            <div class="relative animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="absolute -inset-4 bg-rose-500/20 blur-3xl rounded-full"></div>
                <div class="relative bg-white dark:bg-slate-800 p-8 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700">
                    <img src="{{ asset('img/ternak.png') }}" class="w-full h-auto opacity-100 grayscale" alt="Health Illustration" />
                    <!-- <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-rose-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <span class="text-xl font-black dark:text-white">Health Guard Active</span>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <h2 class="text-3xl font-bold dark:text-white">Keamanan Biologis Terjamin</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Kesehatan adalah aset utama dalam peternakan. Sahabat Farm menyediakan modul khusus untuk mencatat dan menganalisis kondisi kesehatan setiap hewan secara berkala.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900 flex items-center justify-center text-rose-600 shrink-0">✓</div>
                        <span class="font-bold">Jadwal vaksinasi dan vitamin otomatis</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900 flex items-center justify-center text-rose-600 shrink-0">✓</div>
                        <span class="font-bold">Pencatatan riwayat penyakit & penanganan (treatment)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900 flex items-center justify-center text-rose-600 shrink-0">✓</div>
                        <span class="font-bold">Notifikasi karantina untuk hewan terinfeksi</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-12 sm:p-24 text-center">
            <h2 class="text-3xl sm:text-5xl font-black text-white mb-8 leading-tight">Cegah Sebelum Terlambat</h2>
            <p class="text-xl text-slate-400 mb-12 max-w-2xl mx-auto">Gunakan sistem monitoring kami untuk meminimalisir angka kematian ternak.</p>
            <a href="{{ route('pages.contact') }}" class="inline-flex px-10 py-5 bg-rose-600 hover:bg-rose-500 text-white font-black text-xl rounded-2xl transition-all hover:-translate-y-1 shadow-lg shadow-rose-600/20">Aktivasi Health Monitor</a>
        </div>
    </div>
</div>
@endsection
