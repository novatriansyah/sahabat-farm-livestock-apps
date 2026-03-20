@php
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
    $periodString = "$monthName $year";
@endphp

<x-print-layout 
    title="Audit Efisiensi Kandang" 
    type="LAPORAN AUDIT INTERNAL (EFISIENSI KANDANG)" 
    :period="$periodString"
>
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Audit Efisiensi Kandang
        </h2>
    </div>

    <!-- Filter (No-Print) -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm mb-8 p-8 no-print">
        <form method="GET" action="{{ route('reports.audit') }}" class="flex flex-wrap gap-6 items-end">
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
                
                <a href="{{ route('reports.audit', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Pratinjau Cetak
                </a>
                
            </div>
        </form>
    </div>
    @endif

    @if(empty($auditData))
        <div class="p-12 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 text-center text-slate-500">
            <p class="font-medium text-lg italic">Tidak ada aktivitas (kelahiran/kematian) atau populasi pada periode ini.</p>
        </div>
    @else
        <div class="no-break mb-10">
            <h3 class="text-sm font-black mb-6 text-rose-600 dark:text-rose-400 border-l-4 border-rose-500 pl-4 uppercase tracking-[0.2em]">
                Analisis Mortalitas Per Lokasi
            </h3>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Lokasi (Kandang)</th>
                            <th class="text-center whitespace-nowrap">Populasi Aktif</th>
                            <th class="text-center whitespace-nowrap">Kelahiran</th>
                            <th class="text-center whitespace-nowrap">Kematian</th>
                            <th class="text-right whitespace-nowrap">Mortalitas (%)</th>
                            <th class="text-center">Status Efisiensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditData as $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="font-bold text-slate-900 dark:text-white">{{ $row['location'] }}</td>
                            <td class="text-center font-medium">{{ $row['population'] }} Ekor</td>
                            <td class="text-center text-emerald-600 font-black">+{{ $row['births'] }}</td>
                            <td class="text-center text-rose-600 font-black">{{ $row['deaths'] > 0 ? '-'.$row['deaths'] : '0' }}</td>
                            <td class="text-right">
                                <span class="font-black {{ $row['mortality_rate'] > 5 ? 'text-rose-600' : ($row['mortality_rate'] > 2 ? 'text-orange-500' : 'text-emerald-600') }}">
                                    {{ number_format($row['mortality_rate'], 2) }}%
                                </span>
                            </td>
                            <td class="text-center">
                                @if($row['mortality_rate'] > 5)
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10px] font-black uppercase tracking-widest border border-rose-200">KRITIS</span>
                                @elseif($row['mortality_rate'] > 2)
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-orange-100 text-orange-800 text-[10px] font-black uppercase tracking-widest border border-orange-200">WASPADA</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-widest border border-emerald-200">AMAN</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footnote for Audit -->
        <div class="bg-slate-50 border border-slate-200 p-6 rounded-2xl no-print dark:bg-slate-800 dark:border-slate-700">
            <p class="text-xs text-slate-500 leading-relaxed font-medium capitalize">
                <span class="font-bold text-slate-700 dark:text-slate-300">Catatan Audit:</span> Mortality rate dihitung berdasarkan persentase kematian terhadap total populasi. Ambang batas kritis ditetapkan pada > 5% per bulan untuk mitigasi risiko dini.
            </p>
        </div>
    @endif
</x-print-layout>
