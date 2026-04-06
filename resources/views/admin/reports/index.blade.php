@php
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
    $periodString = "$monthName $year";
@endphp

<x-print-layout 
    title="Laporan Kelahiran & Kematian" 
    type="LAPORAN DATA KELAHIRAN & KEMATIAN" 
    :period="$periodString"
>
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-6 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Laporan Kelahiran & Kematian
        </h2>
    </div>

    <!-- Filter (Visible only on Screen) -->
    
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm mb-8 p-8 no-print transition-all hover:shadow-md">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-6 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-widest">Bulan</label>
                <select name="month" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-medium text-slate-700 dark:text-slate-200">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-widest">Tahun</label>
                <select name="year" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-medium text-slate-700 dark:text-slate-200">
                    @for($y=2023; $y<=date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-emerald-500/20 transition-all active:scale-95">Filter</button>
                <a href="{{ route('reports.index', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Pratinjau Cetak
                </a>
                
            </div>
        </form>
    </div>
    @endif

    <!-- Layout on Print: Births and Deaths often on separate pages or sections -->
    
    <!-- Births Section -->
    <div class="mb-12 no-break">
        <h3 class="text-lg font-black mb-6 text-emerald-600 dark:text-emerald-400 border-l-4 border-emerald-500 pl-4 uppercase tracking-widest">
            Data Kelahiran
        </h3>
        @if($births->isEmpty())
            <div class="p-8 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-center text-slate-500">
                Tidak ada data kelahiran pada periode ini.
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Tag ID</th>
                            <th class="text-left">Gender</th>
                            <th class="text-left">Ras / Breed</th>
                            <th class="text-center">Gen</th>
                            <th class="text-right">Bobot</th>
                            <th class="text-left">Parental (Dam/Sire)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($births as $birth)
                        <tr>
                            <td class="whitespace-nowrap">{{ $birth->birth_date->format('d/m/Y') }}</td>
                            <td class="font-bold text-slate-900 dark:text-white">{{ $birth->tag_id }}</td>
                            <td>{{ $birth->gender == 'JANTAN' ? '♂ Jantan' : '♀ Betina' }}</td>
                            <td>{{ $birth->breed->name }}</td>
                            <td class="text-center">{{ $birth->generation ?? '-' }}</td>
                            <td class="text-right">{{ $birth->weightLogs->first()->weight_kg ?? '-' }} kg</td>
                            <td class="text-xs">
                                <span class="block">D: {{ $birth->dam->tag_id ?? '-' }}</span>
                                <span class="block text-slate-400">S: {{ $birth->sire->tag_id ?? '-' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Deaths Section -->
    <div class="mt-12 no-break">
        <h3 class="text-lg font-black mb-6 text-rose-600 dark:text-rose-400 border-l-4 border-rose-500 pl-4 uppercase tracking-widest">
            Data Kematian
        </h3>
        @if($deaths->isEmpty())
            <div class="p-8 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-center text-slate-500">
                Tidak ada data kematian pada periode ini.
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Tag ID</th>
                            <th class="text-left">Gender</th>
                            <th class="text-left">Ras / Breed</th>
                            <th class="text-center">Usia (Bln)</th>
                            <th class="text-right">Estimasi Kerugian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deaths as $death)
                        <tr>
                            <td class="whitespace-nowrap">{{ $death->exit_date->format('d/m/Y') }}</td>
                            <td class="font-bold text-slate-900 dark:text-white">{{ $death->animal->tag_id }}</td>
                            <td>{{ $death->animal->gender == 'JANTAN' ? '♂ Jantan' : '♀ Betina' }}</td>
                            <td>{{ $death->animal->breed->name }}</td>
                            <td class="text-center">{{ number_format($death->animal->birth_date->floatDiffInMonths($death->exit_date), 1) }}</td>
                            <td class="text-right font-bold text-rose-600">Rp {{ number_format(($death->animal->purchase_price ?? 0) + $death->final_hpp, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-print-layout>
