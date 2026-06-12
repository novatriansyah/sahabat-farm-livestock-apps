@extends('layouts.guest')

@section('title', 'Artikel & Insight Peternakan')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    <!-- Animated background patterns -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-[10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-emerald-400/10 dark:bg-emerald-500/5 blur-[120px] animate-pulse" style="animation-duration: 8s"></div>
        <div class="absolute bottom-[20%] right-[-10%] w-[50%] h-[50%] rounded-full bg-teal-400/10 dark:bg-teal-500/5 blur-[120px] animate-pulse" style="animation-duration: 10s"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-24">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Artikel & <span class="text-emerald-500">Insight</span> Peternakan
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Dapatkan info terbaru, tips manajemen ternak, update industri, dan kisah sukses dari kami.
            </p>
        </div>

        <!-- Articles Grid -->
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10 mb-16 animate-fade-in-up" style="animation-delay: 0.2s">
                @foreach ($articles as $article)
                    <div class="group rounded-[2rem] overflow-hidden bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full hover:-translate-y-2">
                        <!-- Thumbnail Container -->
                        <div class="aspect-video relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                            <img src="{{ $article->thumbnail ? Storage::url($article->thumbnail) : asset('img/logo.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 {{ !$article->thumbnail ? 'p-10 opacity-75' : '' }}" alt="{{ $article->title }}">
                            <!-- Floating Badge -->
                            <div class="absolute top-4 left-4 bg-emerald-600 text-white text-xs font-bold px-3.5 py-1.5 rounded-full shadow-md">
                                Edukasi
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-8 flex flex-col flex-grow">
                            <!-- Date & Time -->
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl sm:text-2xl font-black mb-4 dark:text-white line-clamp-2 leading-tight group-hover:text-emerald-500 transition-colors">
                                <a href="{{ route('pages.articles.show', $article->slug) }}">{{ $article->title }}</a>
                            </h3>

                            <!-- Summary -->
                            <p class="text-slate-500 dark:text-slate-400 text-base mb-8 line-clamp-3 leading-relaxed flex-grow font-medium">
                                {{ $article->summary }}
                            </p>

                            <!-- Read Link -->
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 mt-auto">
                                <a href="{{ route('pages.articles.show', $article->slug) }}" class="inline-flex items-center font-bold text-emerald-600 hover:text-emerald-500 transition-colors group/btn">
                                    Baca Selengkapnya
                                    <svg class="ml-2 w-5 h-5 group-hover/btn:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Custom Styled Pagination -->
            <div class="flex justify-center animate-fade-in-up mt-16" style="animation-delay: 0.3s">
                {{ $articles->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white dark:bg-slate-800 rounded-[3rem] border border-slate-200/60 dark:border-slate-700/50 shadow-sm max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M19 20a2 2 0 01-2-2v-1m-1-4l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Belum Ada Artikel</h3>
                <p class="text-slate-500 dark:text-slate-400 px-6 font-medium">Saat ini kami sedang menyiapkan konten-konten berkualitas untuk Anda. Hubungi kami untuk info lebih lanjut.</p>
            </div>
        @endif
    </div>
</div>
@endsection
