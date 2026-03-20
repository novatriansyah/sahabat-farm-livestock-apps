@php
    $monthName = date('F', mktime(0, 0, 0, $month, 10));
    $periodString = "$monthName $year";
@endphp

<x-print-layout 
    title="Laporan Penjualan" 
    type="LAPORAN DATA PENJUALAN" 
    :period="$periodString"
>
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-6 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Laporan Penjualan
        </h2>
    </div>

    <!-- Filter (Visible only on Screen) -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm mb-8 p-8 no-print transition-all hover:shadow-md">
        <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-wrap gap-6 items-end">
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
                
                <a href="{{ route('reports.sales', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Pratinjau Cetak
                </a>
            </div>
        </form>
    </div>
    @endif

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 no-break">
        <div class="p-8 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 print:border-slate-200">
            <p class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mb-2">Total Omset (Revenue)</p>
            <p class="text-3xl font-black text-blue-900 dark:text-blue-100 italic">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        @if(Auth::user()->role === 'PEMILIK')
        <div class="p-8 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50 print:border-slate-200">
            <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] mb-2">Estimasi Profit (Margin)</p>
            <p class="text-3xl font-black text-emerald-900 dark:text-emerald-100 italic">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
        </div>
        @endif
    </div>

    <!-- Sales Table -->
    <div class="no-break">
        <h3 class="text-lg font-black mb-6 text-slate-800 dark:text-slate-200 border-l-4 border-slate-800 pl-4 uppercase tracking-widest print:border-slate-900">
            Daftar Transaksi Penjualan
        </h3>
        
        @if($sales->isEmpty())
            <div class="p-8 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-center text-slate-500">
                Tidak ada riwayat penjualan pada periode ini.
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Tgl Jual</th>
                            <th class="text-left">Tag ID</th>
                            <th class="text-left">Ras / Breed</th>
                            <th class="text-left">Mitra / Investor</th>
                            <th class="text-right">Harga Jual</th>
                            @if(Auth::user()->role === 'PEMILIK')
                            <th class="text-right">HPP Final</th>
                            <th class="text-right">Margin</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="whitespace-nowrap">{{ $sale->exit_date->format('d/m/Y') }}</td>
                            <td class="font-bold text-slate-900 dark:text-white">{{ $sale->animal->tag_id }}</td>
                            <td>{{ $sale->animal->breed->name }}</td>
                            <td>{{ $sale->animal->partner->name ?? '-' }}</td>
                            <td class="text-right font-bold">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                            @if(Auth::user()->role === 'PEMILIK')
                            <td class="text-right text-slate-500">Rp {{ number_format($sale->final_hpp, 0, ',', '.') }}</td>
                            <td class="text-right font-black {{ ($sale->price - $sale->final_hpp) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Rp {{ number_format($sale->price - $sale->final_hpp, 0, ',', '.') }}
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-print-layout>
