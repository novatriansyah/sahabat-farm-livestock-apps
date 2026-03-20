@php
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
    $periodString = "$monthName $year";
@endphp

<x-print-layout 
    title="Laporan Performa ADG" 
    type="LAPORAN ANALISA PERFORMA (ADG)" 
    :period="$periodString"
>
    <!-- Screen Header -->
     @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Performa Pertumbuhan (ADG)
        </h2>
    </div>

    <!-- Filter (No-Print) -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm mb-8 p-8 no-print">
        <form method="GET" action="{{ route('reports.performance') }}" class="flex flex-wrap gap-6 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-widest">Bulan</label>
                <select name="month" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-medium">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-widest">Tahun</label>
                <select name="year" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-medium">
                    @for($y=2023; $y<=date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-all active:scale-95">Filter</button>
                
                <a href="{{ route('reports.performance', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Pratinjau Cetak
                </a>
            </div>
        </form>
    </div>
    @endif

    @if($breedStats->isEmpty())
        <div class="p-10 bg-slate-50 border border-dashed border-slate-200 text-center text-slate-500 italic">
            <p class="font-medium text-lg">Tidak ada data penimbangan aktif pada periode ini.</p>
            <p class="text-xs mt-2 uppercase tracking-widest opacity-50">Silakan lakukan penimbangan rutin di kandang.</p>
        </div>
    @else
        <!-- Breed Summary Table -->
        <div class="mb-12 no-break">
            <h3 class="text-sm font-black mb-6 text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] border-l-4 border-blue-500 pl-4">
                Statistik Rata-Rata ADG per Ras (Breed)
            </h3>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Ras / Breed</th>
                            <th class="text-center">Sampel Data</th>
                            <th class="text-right">Rata-rata ADG</th>
                            <th class="text-right">Max ADG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($breedStats as $breed => $stat)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="font-bold text-slate-900 dark:text-white">{{ $breed }}</td>
                            <td class="text-center font-medium">{{ $stat['count'] }} Ekor</td>
                            <td class="text-right font-black text-blue-600">
                                {{ number_format($stat['avg_adg'], 1) }} <span class="text-[9px] font-bold uppercase">g/hari</span>
                            </td>
                            <td class="text-right text-slate-500 italic">{{ number_format($stat['max_adg'], 1) }} g</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Grids -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 grid-print-2">
            <!-- Top Performers -->
            <div>
                <h3 class="text-sm font-black mb-6 text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] border-l-4 border-emerald-500 pl-4">
                    Top 10 Pertumbuhan Terbaik
                </h3>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="bg-emerald-50">
                                <th class="text-left">Tag ID</th>
                                <th class="text-left">Ras</th>
                                <th class="text-right">ADG (g)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPerformers as $row)
                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                <td class="font-bold text-slate-900 dark:text-white">{{ $row['animal']->tag_id }}</td>
                                <td class="text-slate-500">{{ $row['breed_name'] }}</td>
                                <td class="text-right font-black text-emerald-600">+{{ number_format($row['adg_grams'], 1) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Performers -->
            <div>
                <h3 class="text-sm font-black mb-6 text-rose-600 dark:text-rose-400 uppercase tracking-[0.2em] border-l-4 border-rose-500 pl-4">
                    Atensi (ADG Rendah / Minus)
                </h3>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="bg-rose-50">
                                <th class="text-left">Tag ID</th>
                                <th class="text-left">Lokasi</th>
                                <th class="text-right">ADG (g)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowPerformers as $row)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="font-bold text-slate-900 dark:text-white">{{ $row['animal']->tag_id }}</td>
                                <td class="text-slate-500">{{ $row['location_name'] }}</td>
                                <td class="text-right font-black {{ $row['adg_grams'] < 0 ? 'text-rose-600' : 'text-orange-500' }}">
                                    {{ number_format($row['adg_grams'], 1) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-print-layout>
