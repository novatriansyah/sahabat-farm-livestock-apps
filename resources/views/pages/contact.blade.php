@extends('layouts.guest')

@section('title', 'Hubungi Kami')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Mari <span class="text-emerald-500">Berdiskusi</span>
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Ada pertanyaan tentang fitur atau ingin berkonsultasi tentang implementasi di kandang Anda?
            </p>
        </div>

        <div class="max-w-4xl mx-auto bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-700 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-12 sm:p-20 bg-emerald-600 text-white space-y-12">
                    <h2 class="text-3xl font-bold">Informasi Kontak</h2>
                    <div class="space-y-8">
                        <div class="flex items-start gap-5">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">Email</h4>
                                <p class="text-emerald-100 italic">hello@sahabatfarmindonesia.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-5">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">WhatsApp Support</h4>
                                <p class="text-emerald-100">+62 812-xxxx-xxxx</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-12 sm:p-20">
                    <form class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Nama Lengkap</label>
                            <input type="text" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Nova Triansyah">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Email</label>
                            <input type="email" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="nova@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Pesan</label>
                            <textarea rows="4" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Halo Tim Sahabat Farm..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-5 bg-slate-900 dark:bg-emerald-600 text-white font-black text-xl rounded-2xl hover:shadow-xl transition-all active:scale-95">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
