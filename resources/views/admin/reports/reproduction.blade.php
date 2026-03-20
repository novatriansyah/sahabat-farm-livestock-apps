<x-print-layout 
    title="Laporan Reproduksi Indukan" 
    type="LAPORAN ANALISA REPRODUKSI (INDUKAN)" 
    :period="date('F Y')"
>
    <!-- Screen Header -->
    @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Performa Reproduksi Indukan
        </h2>
        
        <a href="{{ route('reports.reproduction', ['mode' => 'print']) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Laporan
        </a>
    </div>
    @endif

    @if($reproData->isEmpty())
        <div class="p-12 bg-slate-50 dark:bg-slate-800 border border-dashed border-slate-200 text-center text-slate-500 italic">
            <p class="font-medium text-lg">Belum ada data kelahiran dari indukan yang tercatat.</p>
            <p class="text-xs mt-2 uppercase tracking-widest opacity-50">Sistem akan otomatis menghitung statistik setelah ada data kelahiran (Partus).</p>
        </div>
    @else
        <!-- Reproduction Performance Table -->
        <div class="mb-12 no-break">
            <h3 class="text-sm font-black mb-6 text-emerald-600 dark:text-emerald-400 border-l-4 border-emerald-500 pl-4 uppercase tracking-[0.2em]">
                Statistik Kesuburan Indukan (Maternity Performance)
            </h3>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left">Indukan (Tag)</th>
                            <th class="text-left">Ras / Breed</th>
                            <th class="text-center">Frekuensi Hamil</th>
                            <th class="text-center">Total Anak</th>
                            <th class="text-center">Avg Anak/Lahir</th>
                            <th class="text-center">Interval (Hari)</th>
                            <th class="text-right">Kelahiran Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reproData as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="font-black text-slate-900 dark:text-white">{{ $row['dam']->tag_id }}</td>
                            <td class="text-slate-500 font-medium">{{ $row['dam']->breed->name }}</td>
                            <td class="text-center font-bold">{{ $row['total_litters'] }}x</td>
                            <td class="text-center font-black text-emerald-600">{{ $row['total_offspring'] }} <span class="text-[9px] opacity-50">Ekor</span></td>
                            <td class="text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $row['avg_litter_size'] >= 2 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ number_format($row['avg_litter_size'], 1) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($row['avg_interval_days'] > 0)
                                    <span class="font-bold text-slate-700">{{ number_format($row['avg_interval_days'], 0) }} <span class="text-[9px] font-medium opacity-50">Hari</span></span>
                                    <div class="text-[9px] text-slate-400 font-bold uppercase">±{{ number_format($row['avg_interval_days']/30, 1) }} Bulan</div>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="font-bold text-slate-900">{{ $row['last_birth_date'] ? $row['last_birth_date']->format('d/m/Y') : '-' }}</div>
                                <div class="text-[9px] font-black uppercase tracking-tighter {{ $row['days_since_last_birth'] > 240 ? 'text-rose-500' : 'text-slate-400' }}">
                                    {{ $row['days_since_last_birth'] }} Hari Yang Lalu
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reproduction Indicator Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-emerald-50 border border-emerald-100 p-6 dark:bg-emerald-900/20 dark:border-emerald-800/50">
                <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-widest mb-2">Target Interval (KID)</h4>
                <p class="text-[11px] text-emerald-700 dark:text-emerald-400 font-medium leading-relaxed">
                    Target Lambing Interval yang ideal adalah <span class="font-bold">240 hari (8 bulan)</span>. Indukan dengan interval tinggi memerlukan evaluasi nutrisi atau kesehatan reproduksi.
                </p>
            </div>
            <div class="bg-blue-50 border border-blue-100 p-6 dark:bg-blue-900/20 dark:border-blue-800/50">
                <h4 class="text-xs font-black text-blue-800 dark:text-blue-300 uppercase tracking-widest mb-2">Prolifik (Litter Size)</h4>
                <p class="text-[11px] text-blue-700 dark:text-blue-400 font-medium leading-relaxed">
                    Indukan dengan rata-rata anak per kelahiran <span class="font-bold">≥ 2.0</span> dikategorikan sebagai bibit unggul prolifik yang layak dipertahankan sebagai donor genetik.
                </p>
            </div>
        </div>
    @endif
</x-print-layout>
