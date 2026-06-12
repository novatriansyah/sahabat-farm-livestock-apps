@extends('layouts.guest')

@section('title', 'Katalog Ternak')

@section('content')
<div class="relative pt-32 pb-20 overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] rounded-full bg-emerald-400/10 dark:bg-emerald-500/5 blur-[120px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white mb-8 leading-tight animate-fade-in-up">
                Katalog <span class="text-emerald-500">Ternak</span>
            </h1>
            <p class="text-lg sm:text-2xl text-slate-600 dark:text-slate-400 font-medium animate-fade-in-up" style="animation-delay: 0.1s">
                Jelajahi koleksi ternak berkualitas kami. Hubungi langsung via WhatsApp untuk informasi lebih lanjut.
            </p>
        </div>

        {{-- Filters --}}
        <div class="mb-12 animate-fade-in-up" style="animation-delay: 0.2s">
            <form method="GET" action="{{ route('pages.catalogue') }}" class="flex flex-col sm:flex-row gap-4 max-w-2xl mx-auto">
                <select name="breed_id" class="flex-1 px-5 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none text-slate-700 dark:text-slate-200 font-medium">
                    <option value="">Semua Jenis</option>
                    @foreach($breeds as $breed)
                        <option value="{{ $breed->id }}" {{ request('breed_id') == $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..." class="flex-1 px-5 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-emerald-500 outline-none text-slate-700 dark:text-slate-200 font-medium">
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl transition-all hover:-translate-y-0.5 active:scale-95 shadow-lg shadow-emerald-500/20">
                    Filter
                </button>
            </form>
        </div>

        {{-- Results --}}
        @if($animals->isEmpty())
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-700 dark:text-slate-300 mb-2">Belum Ada Ternak</h3>
                <p class="text-slate-500 font-medium">Saat ini belum ada ternak yang tersedia di katalog.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($animals as $animal)
                <div class="group bg-white dark:bg-slate-800 rounded-[2rem] overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 animate-fade-in-up" style="animation-delay: {{ 0.1 * $loop->index }}s">
                    {{-- Photo --}}
                    <div class="relative h-56 bg-slate-100 dark:bg-slate-700 overflow-hidden">
                        @if($animal->photos->count() > 0)
                            <img src="{{ Storage::url($animal->photos->first()->photo_url) }}" alt="{{ $animal->full_breed }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        {{-- Breed Badge --}}
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm rounded-full text-xs font-bold text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50">
                                {{ $animal->full_breed }}
                            </span>
                        </div>
                        {{-- Gender Badge --}}
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1.5 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm rounded-full text-xs font-bold {{ $animal->gender === 'JANTAN' ? 'text-blue-600' : 'text-pink-600' }}">
                                {{ $animal->gender === 'JANTAN' ? '♂ Jantan' : '♀ Betina' }}
                            </span>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-6">
                        {{-- Stats Row --}}
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-medium">{{ $animal->age_string }}</span>
                            </div>
                            @if($animal->latestWeightLog)
                            <div class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                <span class="font-medium">{{ $animal->latestWeightLog->weight_kg }} kg</span>
                            </div>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($animal->sale_description)
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-5 line-clamp-2 leading-relaxed">{{ $animal->sale_description }}</p>
                        @endif

                        {{-- Price --}}
                        <div class="mb-5">
                            <span class="text-2xl font-black text-slate-900 dark:text-white">Rp {{ number_format($animal->sale_price, 0, ',', '.') }}</span>
                        </div>

                        {{-- WhatsApp CTA --}}
                        @if($whatsapp)
                        @php
                            $waText = urlencode("Halo, saya tertarik dengan ternak {$animal->full_breed} di katalog Sahabat Farm Indonesia. Bisa minta info lebih lanjut?");
                        @endphp
                        <a href="https://wa.me/{{ $whatsapp }}?text={{ $waText }}" target="_blank" class="flex items-center justify-center gap-2 w-full px-6 py-3.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition-all hover:-translate-y-0.5 active:scale-95 shadow-lg shadow-green-500/20">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.616l4.584-1.453A11.949 11.949 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.239 0-4.308-.726-5.993-1.957l-.42-.307-2.724.864.894-2.657-.336-.434A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                            Hubungi via WhatsApp
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-16 flex justify-center">
                {{ $animals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
