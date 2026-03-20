@php
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
    $periodString = "$monthName $year";
@endphp

<x-print-layout 
    title="Laporan Operasional Pakan" 
    type="LAPORAN OPERASIONAL (PENGGUNAAN PAKAN)" 
    :period="$periodString"
>
    <!-- Screen Header -->
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Operasional & Pakan
        </h2>
    </div>

    <!-- Filter (No-Print) -->
    
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm mb-8 p-8 no-print">
        <form method="GET" action="{{ route('reports.operational') }}" class="flex flex-wrap gap-6 items-end">
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
            
            <a href="{{ route('reports.operational', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Pratinjau Cetak
            </a>
            
            </div>
        </form>
    </div>
    @endif

    <!-- Summary -->
    <div class="mb-10 no-break">
        <h3 class="text-sm font-black mb-6 text-orange-600 dark:text-orange-400 border-l-4 border-orange-500 pl-4 uppercase tracking-[0.2em]">
            Ringkasan Biaya Pakan
        </h3>
        
        <div class="p-8 bg-orange-50 dark:bg-orange-900/30 border border-orange-100 dark:border-orange-800/50 mb-8 print:border-slate-200">
            <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2">Total Estimasi Biaya Pakan</p>
            <p class="text-4xl font-black text-orange-900 dark:text-orange-100 italic">Rp {{ number_format($totalCost, 0, ',', '.') }}</p>
            <p class="text-[9px] text-orange-600 dark:text-orange-400 mt-2 font-bold uppercase italic opacity-60">* Perhitungan berdasarkan rata-rata harga beli inventory</p>
        </div>

        @if(empty($usageSummary))
            <div class="p-10 bg-slate-50 rounded-3xl border border-dashed border-slate-200 text-center text-slate-500 italic">
                Tidak ada riwayat penggunaan pakan pada periode ini.
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Nama Item Pakan</th>
                            <th class="text-center">Satuan</th>
                            <th class="text-right">Qty Terpakai</th>
                            <th class="text-right">Harga Rata-rata</th>
                            <th class="text-right">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usageSummary as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="font-bold text-slate-900 dark:text-white">{{ $item['item_name'] }}</td>
                            <td class="text-center font-medium">{{ $item['unit'] }}</td>
                            <td class="text-right font-black text-slate-700 dark:text-slate-300">{{ number_format($item['qty_used'], 1) }}</td>
                            <td class="text-right text-slate-500 italic">Rp {{ number_format($item['avg_price'], 0, ',', '.') }}</td>
                            <td class="text-right font-black text-orange-600">Rp {{ number_format($item['total_cost'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Usage by Location -->
    <div class="mt-12 no-break">
        <h3 class="text-sm font-black mb-6 text-slate-900 dark:text-white uppercase tracking-[0.2em] border-l-4 border-slate-900 pl-4">
            Penggunaan Pakan Per Kandang
        </h3>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left">Lokasi (Kandang / Area)</th>
                        <th class="px-6 py-4 text-right">Total Volume (Mixed Units)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locationSummary as $locName => $qty)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">{{ $locName }}</td>
                        <td class="px-6 py-4 text-right font-black text-slate-700 dark:text-slate-300">{{ number_format($qty, 1) }} Units</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
