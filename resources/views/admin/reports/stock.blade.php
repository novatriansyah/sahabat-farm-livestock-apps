<x-print-layout 
    title="Laporan Stok & Populasi" 
    type="LAPORAN DATA STOK & POPULASI" 
    :period="date('F Y')"
>
    <!-- Screen Header -->
    @if(request('mode') !== 'print')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 no-print gap-6">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Stok & Populasi
        </h2>
        
        <form method="GET" action="{{ route('reports.stock') }}" class="flex items-center gap-3">
            <select name="ownership" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-xl focus:ring-emerald-500 font-bold px-4 py-2.5 shadow-sm transition-all hover:bg-slate-50">
                <option value="">Semua (Internal & Mitra)</option>
                <option value="INTERNAL" {{ request('ownership') === 'INTERNAL' ? 'selected' : '' }}>Internal (SFI)</option>
                <option value="MITRA" {{ request('ownership') === 'MITRA' ? 'selected' : '' }}>Mitra / Investor</option>
            </select>
            
            <select name="location_id" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-xl focus:ring-emerald-500 font-bold px-4 py-2.5 shadow-sm transition-all hover:bg-slate-50">
                <option value="">Semua Kandang</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                @endforeach
            </select>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.stock', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </a>

                <a href="{{ route('reports.stock.export', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-600/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel
                </a>
            </div>
        </form>
    </div>
    @endif

    <!-- Summary Statistics (Optimized for Print Grid) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10 no-break">
        <div class="p-8 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50">
            <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-2">Total Populasi</p>
            <p class="text-4xl font-black text-emerald-900 dark:text-emerald-100 italic">{{ $byGender['TOTAL'] }} <span class="text-sm font-bold opacity-50 not-italic">Ekor</span></p>
        </div>
        @foreach($byAgeGroup as $groupName => $count)
        <div class="p-8 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">{{ $groupName }}</p>
            <p class="text-4xl font-black text-slate-800 dark:text-slate-100 italic">{{ $count }} <span class="text-sm font-bold opacity-30 not-italic">Ekor</span></p>
        </div>
        @endforeach
    </div>

    <!-- Population Breakdowns -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12 grid-print-2">
        <!-- By Location -->
        <div class="no-break">
            <h3 class="text-sm font-black mb-6 text-slate-900 dark:text-white uppercase tracking-[0.2em] border-l-4 border-slate-900 pl-4">
                Populasi Per Kandang
            </h3>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Lokasi</th>
                            <th class="text-center">♂ Jantan</th>
                            <th class="text-center">♀ Betina</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byLocation as $locName => $stats)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="font-bold text-slate-700 dark:text-slate-300">{{ $locName }}</td>
                            <td class="text-center font-medium">{{ $stats['male'] }}</td>
                            <td class="text-center font-medium">{{ $stats['female'] }}</td>
                            <td class="text-right font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- By Breed -->
        <div class="no-break">
            <h3 class="text-sm font-black mb-6 text-slate-900 dark:text-white uppercase tracking-[0.2em] border-l-4 border-slate-900 pl-4">
                Populasi Per Ras
            </h3>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Ras / Breed</th>
                            <th class="text-right">Jumlah Populasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byBreed as $breedName => $count)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="font-bold text-slate-700 dark:text-slate-300">{{ $breedName }}</td>
                            <td class="text-right font-black text-slate-900 dark:text-white">{{ $count }} Ekor</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Full Detail -->
    <div class="mt-12 no-break">
        <h3 class="text-sm font-black mb-6 text-slate-900 dark:text-white uppercase tracking-[0.2em] border-l-4 border-slate-900 pl-4">
            Rincian Detail Ternak Aktif
        </h3>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left">Tag ID</th>
                        <th class="text-left">Gender</th>
                        <th class="text-left">Breed</th>
                        <th class="text-left">Usia</th>
                        <th class="text-left">Lokasi</th>
                        <th class="text-left">Status Fisik</th>
                        @if(Auth::user()->role === 'PEMILIK')
                        <th class="text-left">Mitra/Investor</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($animals as $animal)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ $animal->tag_id }}</td>
                        <td class="whitespace-nowrap">{{ $animal->gender == 'JANTAN' ? '♂ Jantan' : '♀ Betina' }}</td>
                        <td>{{ $animal->breed->name }}</td>
                        <td class="whitespace-nowrap">{{ number_format($animal->birth_date->diffInMonths(now()), 1) }} bln</td>
                        <td>{{ $animal->location->name ?? '-' }}</td>
                        <td>{{ $animal->physStatus->name ?? '-' }}</td>
                        @if(Auth::user()->role === 'PEMILIK')
                        <td class="text-xs">{{ $animal->partner->name ?? '-' }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(method_exists($animals, 'links'))
                <div class="p-6 no-print border-t border-slate-100">
                    {{ $animals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-print-layout>
