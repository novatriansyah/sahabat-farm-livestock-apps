<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Konten Website</h2>
            <p class="text-sm text-gray-500 mt-1">Edit teks dan angka yang tampil di halaman depan. Struktur dan desain tetap terkunci.</p>
        </div>

        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.site-content.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Accordion Container --}}
            <div class="space-y-4" x-data="{ open: 'whatsapp' }">

                {{-- WhatsApp --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'whatsapp' ? '' : 'whatsapp'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.616l4.584-1.453A11.949 11.949 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.239 0-4.308-.726-5.993-1.957l-.42-.307-2.724.864.894-2.657-.336-.434A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">WhatsApp</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'whatsapp'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'whatsapp'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor WhatsApp (format: 628xxx)</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $whatsapp) }}" placeholder="6281234567890" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('whatsapp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Hero Section --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'hero' ? '' : 'hero'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Hero Section (Title)</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'hero'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'hero'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 grid gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Badge Text</label>
                            <input type="text" name="hero[badge]" value="{{ old('hero.badge', $hero['badge'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Headline</label>
                                <input type="text" name="hero[headline]" value="{{ old('hero.headline', $hero['headline'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Headline Accent (warna gradient)</label>
                                <input type="text" name="hero[headline_accent]" value="{{ old('hero.headline_accent', $hero['headline_accent'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subheadline</label>
                            <textarea name="hero[subheadline]" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('hero.subheadline', $hero['subheadline'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Hero Showcase --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'showcase' ? '' : 'showcase'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Media Showcase (Tabs Hero)</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'showcase'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'showcase'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        <p class="text-xs text-gray-550 dark:text-gray-400">Kelola 3 tab showcase visual yang muncul di bawah Hero section (bisa berupa foto dasbor atau video tutorial walkthrough).</p>
                        
                        @for($i = 0; $i < 3; $i++)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3 border border-gray-200 dark:border-gray-600">
                            <p class="text-xs font-bold text-gray-500 uppercase">Tab #{{ $i + 1 }}</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-500">Nama Tab</label>
                                    <input type="text" name="showcase[{{ $i }}][tab_title]" value="{{ old("showcase.$i.tab_title", $showcase[$i]['tab_title'] ?? '') }}" placeholder="Contoh: Dasbor Utama" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-500">Tipe Media</label>
                                    <select name="showcase[{{ $i }}][media_type]" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        <option value="IMAGE" {{ ($showcase[$i]['media_type'] ?? '') === 'IMAGE' ? 'selected' : '' }}>Foto / Screenshot (WebP)</option>
                                        <option value="VIDEO" {{ ($showcase[$i]['media_type'] ?? '') === 'VIDEO' ? 'selected' : '' }}>Video Walkthrough (MP4)</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-xs font-medium text-gray-500">File Media (Kosongkan jika tidak diganti)</label>
                                @if(!empty($showcase[$i]['path']))
                                    <div class="mb-2">
                                        @if(($showcase[$i]['media_type'] ?? '') === 'VIDEO')
                                            <span class="text-xs text-green-600 font-semibold flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Video walkthrough terunggah
                                            </span>
                                        @else
                                            <img src="{{ Str::startsWith($showcase[$i]['path'], 'img/') ? asset($showcase[$i]['path']) : Storage::url($showcase[$i]['path']) }}" class="h-16 w-auto rounded border dark:border-gray-600">
                                        @endif
                                    </div>
                                @endif
                                <input type="file" name="showcase_files[{{ $i }}]" class="block w-full text-xs text-gray-900 border border-gray-300 rounded bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- Stats --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'stats' ? '' : 'stats'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Statistik Angka</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'stats'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'stats'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-3">
                        @for($i = 0; $i < 4; $i++)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-500">Angka #{{ $i + 1 }}</label>
                                <input type="text" name="stats[{{ $i }}][number]" value="{{ old("stats.$i.number", $stats[$i]['number'] ?? '') }}" placeholder="500+" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-500">Label #{{ $i + 1 }}</label>
                                <input type="text" name="stats[{{ $i }}][label]" value="{{ old("stats.$i.label", $stats[$i]['label'] ?? '') }}" placeholder="Peternak Terdaftar" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-550 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- Features Header --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'feat_header' ? '' : 'feat_header'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Header & Kartu Fitur</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'feat_header'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'feat_header'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul Section Fitur</label>
                            <input type="text" name="features_header[title]" value="{{ old('features_header.title', $featuresHeader['title'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subtitle Section Fitur</label>
                            <input type="text" name="features_header[subtitle]" value="{{ old('features_header.subtitle', $featuresHeader['subtitle'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">6 Kartu Fitur (ikon & link tetap, teks bisa diubah)</p>
                        @for($i = 0; $i < 6; $i++)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                            <p class="text-xs font-bold text-gray-500 uppercase">Kartu #{{ $i + 1 }} — {{ $featureRefs[$i]['icon'] ?? '' }}</p>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-500">Judul</label>
                                <input type="text" name="features[{{ $i }}][title]" value="{{ old("features.$i.title", $features[$i]['title'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-500">Deskripsi</label>
                                <textarea name="features[{{ $i }}][desc]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old("features.$i.desc", $features[$i]['desc'] ?? '') }}</textarea>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- About (Homepage) --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'about' ? '' : 'about'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Tentang Kami (Homepage)</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'about'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'about'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Heading</label>
                            <textarea name="about[heading]" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('about.heading', $about['heading'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf</label>
                            <textarea name="about[paragraph]" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('about.paragraph', $about['paragraph'] ?? '') }}</textarea>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto Tentang Kami (Homepage)</label>
                            @if(!empty($about['image']))
                                <div class="mb-2">
                                    <img src="{{ Storage::url($about['image']) }}" class="h-20 w-auto rounded border dark:border-gray-600">
                                </div>
                            @endif
                            <input type="file" name="about_image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                        </div>

                        <p class="text-xs text-gray-550 font-medium uppercase tracking-wider">Checklist Items (4)</p>
                        @for($i = 0; $i < 4; $i++)
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Item #{{ $i + 1 }}</label>
                            <input type="text" name="about[checklist][{{ $i }}]" value="{{ old("about.checklist.$i", $about['checklist'][$i] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- About Us Page --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'about_us_page' ? '' : 'about_us_page'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Halaman Tentang Kami</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'about_us_page'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'about_us_page'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Heading Utama</label>
                            <input type="text" name="about_us[heading]" value="{{ old('about_us.heading', $aboutUs['heading'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subheading Utama</label>
                            <textarea name="about_us[subheading]" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('about_us.subheading', $aboutUs['subheading'] ?? '') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul Visi</label>
                                <input type="text" name="about_us[vision_title]" value="{{ old('about_us.vision_title', $aboutUs['vision_title'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul Misi</label>
                                <input type="text" name="about_us[mission_title]" value="{{ old('about_us.mission_title', $aboutUs['mission_title'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Teks Visi</label>
                            <textarea name="about_us[vision_text]" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('about_us.vision_text', $aboutUs['vision_text'] ?? '') }}</textarea>
                        </div>
                        
                        <p class="text-xs text-gray-550 font-medium uppercase tracking-wider">Checklist Misi (3)</p>
                        @for($i = 0; $i < 3; $i++)
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Misi #{{ $i + 1 }}</label>
                            <input type="text" name="about_us[mission_checklist][{{ $i }}]" value="{{ old("about_us.mission_checklist.$i", $aboutUs['mission_checklist'][$i] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        @endfor

                        <p class="text-xs text-gray-550 font-medium uppercase tracking-wider">4 Grid Team / Galeri Foto</p>
                        <div class="grid grid-cols-2 gap-4">
                            @for($t = 1; $t <= 4; $t++)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">Foto #{{ $t }}</label>
                                @if(!empty($aboutUs['images']["team_$t"]))
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($aboutUs['images']["team_$t"]) }}" class="h-16 w-auto rounded border dark:border-gray-600">
                                    </div>
                                @endif
                                <input type="file" name="about_us_team_{{ $t }}" class="block w-full text-xs text-gray-900 border border-gray-300 rounded bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Testimonials --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'testimonials' ? '' : 'testimonials'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Testimoni</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'testimonials'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'testimonials'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        @for($i = 0; $i < 3; $i++)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                            <p class="text-xs font-bold text-gray-500 uppercase">Testimoni #{{ $i + 1 }}</p>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-500">Kutipan</label>
                                <textarea name="testimonials[{{ $i }}][quote]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old("testimonials.$i.quote", $testimonials[$i]['quote'] ?? '') }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-500">Nama</label>
                                    <input type="text" name="testimonials[{{ $i }}][name]" value="{{ old("testimonials.$i.name", $testimonials[$i]['name'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-500">Jabatan / Peran</label>
                                    <input type="text" name="testimonials[{{ $i }}][role]" value="{{ old("testimonials.$i.role", $testimonials[$i]['role'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- CTA --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'cta' ? '' : 'cta'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Call to Action</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'cta'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'cta'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Headline</label>
                            <input type="text" name="cta[headline]" value="{{ old('cta.headline', $cta['headline'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subheadline</label>
                            <textarea name="cta[subheadline]" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('cta.subheadline', $cta['subheadline'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <button type="button" @click="open = open === 'footer' ? '' : 'footer'" class="w-full flex items-center justify-between p-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">Footer</span>
                        </div>
                        <svg :class="{'rotate-180': open === 'footer'}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 'footer'" x-collapse class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tagline Footer</label>
                        <textarea name="footer_tagline" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('footer_tagline', $footerTagline) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
