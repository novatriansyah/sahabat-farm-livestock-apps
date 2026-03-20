@extends('layouts.guest')

@section('title', 'Sales Tracking')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Analisis <span class="text-blue-500">Penjualan</span> & Profit
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Lacak setiap rupiah transaksi dan ketahui performa finansial bisnis Anda secara real-time.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-32">
             <div class="relative animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="absolute -inset-4 bg-blue-500/20 blur-3xl rounded-full"></div>
                <div class="relative bg-white dark:bg-slate-800 p-8 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700">
                    <img src="{{ asset('img/penjualan.png') }}" class="w-full h-auto opacity-100 grayscale" alt="Sales Illustration" />
                    <!-- <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-blue-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <span class="text-xl font-black dark:text-white">Profit Tracker</span>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <h2 class="text-3xl font-bold dark:text-white">Kendali Finansial Total</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Jangan menebak-nebak laba Anda. Sahabat Farm membantu Anda menghitung HPP (Harga Pokok Penjualan) secara otomatis berdasarkan riwayat pakan dan perawatan.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 shrink-0">✓</div>
                        <span class="font-bold">Laporan laba rugi instan per periode</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 shrink-0">✓</div>
                        <span class="font-bold">Digital invoicing & bukti pembayaran</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 shrink-0">✓</div>
                        <span class="font-bold">Pencatatan data pembeli & riwayat transaksi</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-12 sm:p-24 text-center">
            <h2 class="text-3xl sm:text-5xl font-black text-white mb-8 leading-tight">Tingkatkan Profit Bisnis</h2>
            <p class="text-xl text-slate-400 mb-12 max-w-2xl mx-auto">Gunakan data untuk menentukan kapan waktu terbaik untuk menjual ternak Anda.</p>
            <a href="{{ route('pages.contact') }}" class="inline-flex px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black text-xl rounded-2xl transition-all hover:-translate-y-1 shadow-lg shadow-blue-600/20">Mulai Analisis Sales</a>
        </div>
    </div>
</div>
@endsection
