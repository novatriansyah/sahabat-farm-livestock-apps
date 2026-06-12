@extends('layouts.guest')

@section('title', $article->title)

@section('content')
@php
    $embedUrl = '';
    if (!empty($article->video_url)) {
        if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $article->video_url, $match)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $match[1];
        }
    }
@endphp

<div class="relative pt-32 pb-20 overflow-hidden">
    <!-- Animated background decoration -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-[10%] left-[-15%] w-[45%] h-[45%] rounded-full bg-emerald-400/10 dark:bg-emerald-500/5 blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[20%] right-[-15%] w-[45%] h-[45%] rounded-full bg-teal-400/10 dark:bg-teal-500/5 blur-[120px] animate-pulse"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm font-semibold text-slate-500 dark:text-slate-400 animate-fade-in-up" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('pages.articles.index') }}" class="ml-1 md:ml-3 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Artikel</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="ml-1 md:ml-3 text-slate-400 dark:text-slate-500 line-clamp-1 max-w-[150px] sm:max-w-xs">{{ $article->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Two Column Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 sm:gap-16 items-start">
            
            <!-- Article Body Column -->
            <article class="lg:col-span-8 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/50 shadow-sm p-6 sm:p-12 animate-fade-in-up" style="animation-delay: 0.1s">
                
                <!-- Category/Badge & Meta Info -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-xs font-bold px-3.5 py-1.5 rounded-full">
                        Edukasi
                    </span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
                    </span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Sahabat Farm Indonesia
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white leading-tight mb-8">
                    {{ $article->title }}
                </h1>

                <!-- Featured Media (Video or Image) -->
                @if (!empty($embedUrl))
                    <div class="aspect-video w-full rounded-[2rem] overflow-hidden shadow-lg border border-slate-200 dark:border-slate-700 bg-slate-900 mb-10">
                        <iframe class="w-full h-full" src="{{ $embedUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                @elseif ($article->thumbnail)
                    <div class="aspect-video w-full rounded-[2rem] overflow-hidden shadow-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 mb-10">
                        <img src="{{ Storage::url($article->thumbnail) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Article Content Wrapper with Premium Typography Styling -->
                <div class="article-content text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                    {!! $article->content !!}
                </div>

                <!-- Share Link / Back Link Footer -->
                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-700/60 flex flex-wrap justify-between items-center gap-4">
                    <a href="{{ route('pages.articles.index') }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors group">
                        <svg class="mr-2 w-5 h-5 group-hover:-translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                        Kembali ke Artikel
                    </a>
                </div>
            </article>

            <!-- Sidebar Column -->
            <div class="lg:col-span-4 space-y-10 animate-fade-in-up" style="animation-delay: 0.2s">
                
                <!-- Recent Articles Widget -->
                @if($recentArticles->count() > 0)
                    <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/50 shadow-sm p-8">
                        <h3 class="text-xl font-black mb-6 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-700/60">Artikel Terbaru</h3>
                        <div class="space-y-6">
                            @foreach ($recentArticles as $recent)
                                <a href="{{ route('pages.articles.show', $recent->slug) }}" class="group flex gap-4 items-start">
                                    <div class="w-20 h-20 rounded-2xl overflow-hidden shrink-0 bg-slate-100 dark:bg-slate-900 border border-slate-100 dark:border-slate-700">
                                        <img src="{{ $recent->thumbnail ? Storage::url($recent->thumbnail) : asset('img/logo.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 {{ !$recent->thumbnail ? 'p-4 opacity-75' : '' }}" alt="{{ $recent->title }}">
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">
                                            {{ $recent->published_at ? $recent->published_at->format('d M Y') : $recent->created_at->format('d M Y') }}
                                        </span>
                                        <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 line-clamp-2 leading-snug group-hover:text-emerald-500 transition-colors">
                                            {{ $recent->title }}
                                        </h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Call To Action Widget -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-[2rem] shadow-xl p-8 relative overflow-hidden group">
                    <!-- Overlay lighting -->
                    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-white/10 blur-xl pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
                    
                    <div class="relative z-10 space-y-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        </div>
                        <h4 class="text-xl font-black leading-tight">Butuh Konsultasi Manajemen Peternakan?</h4>
                        <p class="text-emerald-100 text-sm leading-relaxed font-medium">Tim Sahabat Farm Indonesia siap membantu digitalisasi peternakan Anda dengan solusi pintar.</p>
                        @php $waContact = \App\Models\FarmSetting::get('whatsapp_number'); @endphp
                        <a href="{{ $waContact ? 'https://wa.me/' . $waContact : route('pages.contact') }}" {{ $waContact ? 'target=_blank' : '' }} class="inline-flex w-full items-center justify-center px-6 py-4 bg-white text-emerald-700 font-extrabold rounded-2xl transition-all hover:-translate-y-0.5 active:scale-95 text-center shadow-lg shadow-emerald-950/20">
                            Hubungi Kami
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Premium visual styling for user-generated Quill HTML content */
    .article-content {
        font-size: 1.125rem;
    }
    .article-content p {
        margin-bottom: 1.5rem;
    }
    .article-content h1, 
    .article-content h2, 
    .article-content h3, 
    .article-content h4 {
        color: rgb(15 23 42);
        font-weight: 800;
        line-height: 1.35;
        margin-top: 2.5rem;
        margin-bottom: 1.25rem;
    }
    .dark .article-content h1,
    .dark .article-content h2,
    .dark .article-content h3,
    .dark .article-content h4 {
        color: rgb(248 250 252);
    }
    .article-content h1 { font-size: 2.25rem; }
    .article-content h2 { font-size: 1.875rem; }
    .article-content h3 { font-size: 1.5rem; }
    .article-content h4 { font-size: 1.25rem; }
    
    .article-content ul, 
    .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    .article-content ul {
        list-style-type: disc;
    }
    .article-content ol {
        list-style-type: decimal;
    }
    .article-content li {
        margin-bottom: 0.5rem;
    }
    
    .article-content blockquote {
        border-left: 4px solid rgb(16 185 129);
        background-color: rgb(248 250 252);
        padding: 1.5rem;
        font-style: italic;
        border-radius: 0 1rem 1rem 0;
        margin-bottom: 1.5rem;
        color: rgb(71 85 105);
    }
    .dark .article-content blockquote {
        background-color: rgb(30 41 59 / 0.5);
        color: rgb(203 213 225);
    }
    
    .article-content img {
        border-radius: 1.5rem;
        margin: 2.5rem auto;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        max-width: 100%;
        height: auto;
    }
    .article-content iframe {
        width: 100%;
        aspect-ratio: 16 / 9;
        border-radius: 1.5rem;
        margin: 2.5rem auto;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    }
</style>
@endpush
