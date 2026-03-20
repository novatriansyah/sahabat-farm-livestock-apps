@extends('layouts.guest')

@section('title', 'Manajemen Pakan')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Optimasi <span class="text-indigo-500">Pakan</span> & Nutrisi
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Kendalikan biaya pakan Anda dengan manajemen stok dan formulasi nutrisi yang presisi.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-32">
            <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <h2 class="text-3xl font-bold dark:text-white">Efisiensi Pakan Maksimal</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Pakan menyumbang lebih dari 70% biaya operasional. Kurangi pemborosan dan pastikan stok selalu tersedia dengan modul manajemen gudang kami.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 shrink-0">✓</div>
                        <span class="font-bold">Manajemen stok real-time (FIFO)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 shrink-0">✓</div>
                        <span class="font-bold">Laporan konsumsi pakan per koloni</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 shrink-0">✓</div>
                        <span class="font-bold">Peringatan stok kritis otomatis</span>
                    </li>
                </ul>
            </div>
            <div class="relative animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="absolute -inset-4 bg-indigo-500/20 blur-3xl rounded-full"></div>
                <div class="relative bg-white dark:bg-slate-800 p-8 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700">
                    <img src="{{ asset('img/pakan.png') }}" class="w-full h-auto opacity-100 grayscale" alt="Feed Illustration" />
                    <!-- <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-indigo-500 rounded-3xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <span class="text-xl font-black dark:text-white">Storage Optimized</span>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-12 sm:p-24 text-center">
            <h2 class="text-3xl sm:text-5xl font-black text-white mb-8 leading-tight">Hemat Biaya Operasional</h2>
            <p class="text-xl text-slate-400 mb-12 max-w-2xl mx-auto">Bergabunglah dengan peternak lain yang telah menghemat hingga 15% biaya pakan.</p>
            <a href="{{ route('pages.contact') }}" class="inline-flex px-10 py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xl rounded-2xl transition-all hover:-translate-y-1 shadow-lg shadow-indigo-600/20">Kelola Stok Sekarang</a>
        </div>
    </div>
</div>
@endsection
