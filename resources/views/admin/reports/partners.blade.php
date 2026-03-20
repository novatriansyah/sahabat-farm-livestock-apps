<x-print-layout 
    title="Laporan Investasi Mitra" 
    type="LAPORAN INVESTASI & MUTASI MITRA" 
    :period="date('d F Y')"
>
    <!-- Screen Header -->
     @if(request('mode') !== 'print')
    <div class="flex justify-between items-center mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Manajemen Mitra (Investor)
        </h2>
    </div>

    <!-- Partner Selection (No-Print) -->
    @if(Auth::user()->role === 'PEMILIK')
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm mb-8 p-8 no-print">
        <form method="GET" action="{{ route('reports.partners') }}" class="flex flex-wrap gap-6 items-end">
            <div class="flex-1 min-w-[300px]">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-widest">Pilih Mitra / Investor</label>
                <select name="partner_id" class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 font-bold" onchange="this.form.submit()">
                    <option value="">-- Pilih Nama Mitra --</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ $targetPartnerId == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.partners', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex items-center gap-2 no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Pratinjau Cetak
                </a>
            </div>
        </form>
    </div>
    @endif
    @endif

    @if(!$targetPartnerId)
        <div class="p-12 bg-amber-50 dark:bg-amber-900/20 border border-dashed border-amber-200 text-center text-amber-700 font-bold italic">
            Silakan pilih Mitra / Investor untuk melihat ringkasan aset dan profit.
        </div>
    @else
        <!-- Summary Dashboard (Print Optimized) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10 grid-print-4">
            <div class="p-6 bg-slate-50 dark:bg-slate-800 border border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Populasi Ternak</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $summary['total_animals'] }} <span class="text-xs font-bold opacity-40">Ekor</span></p>
            </div>
            <div class="p-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-100">
                <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-2">Aset Berjalan</p>
                <p class="text-2xl font-black text-blue-900 dark:text-blue-100 italic font-serif">Rp {{ number_format($summary['asset_value'], 0, ',', '.') }}</p>
            </div>
            <div class="p-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100">
                <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-2">Realisasi Profit</p>
                <p class="text-2xl font-black text-emerald-900 dark:text-emerald-100 italic font-serif">Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}</p>
            </div>
            <div class="p-6 bg-purple-50 dark:bg-purple-900/30 border border-purple-100">
                <p class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-2">Total Omset</p>
                <p class="text-2xl font-black text-purple-900 dark:text-white italic font-serif">Rp {{ number_format($summary['total_sales_revenue'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Active Assets -->
        <div class="mb-12">
            <h3 class="text-sm font-black mb-6 text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] border-l-4 border-blue-500 pl-4">
                Portofolio Aset Ternak Aktif
            </h3>
            
            @if($activeAnimals->isEmpty())
                <p class="p-8 text-center text-slate-400 italic bg-slate-50 rounded-2xl">Mitra ini tidak memiliki aset ternak aktif saat ini.</p>
            @else
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="text-left">Tag ID</th>
                                <th class="text-left">Ras / Breed</th>
                                <th class="text-left">Gender</th>
                                <th class="text-left">Lokasi</th>
                                <th class="text-right">Investasi Awal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeAnimals as $animal)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="font-bold text-slate-900 dark:text-white">{{ $animal->tag_id }}</td>
                                <td>{{ $animal->breed->name }}</td>
                                <td class="text-xs font-bold">{{ $animal->gender }}</td>
                                <td>{{ $animal->location->name ?? '-' }}</td>
                                <td class="text-right font-black text-slate-700 italic">Rp {{ number_format($animal->purchase_price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Sales History -->
        <div class="mt-12">
            <h3 class="text-sm font-black mb-6 text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] border-l-4 border-emerald-500 pl-4">
                Riwayat Realisasi Profit (Exit Strategy)
            </h3>
            
            @if($salesHistory->isEmpty())
                <p class="p-8 text-center text-slate-400 italic bg-slate-50 rounded-2xl">Belum ada realisasi penjualan untuk portofolio ini.</p>
            @else
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 shadow-sm print:border-none">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="text-left">Tanggal</th>
                                <th class="text-left">Tag ID</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-right">HPP Final</th>
                                <th class="text-right">Net Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesHistory as $sale)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="font-medium whitespace-nowrap">{{ $sale->exit_date->format('d/m/Y') }}</td>
                                <td class="font-bold text-slate-900 dark:text-white">{{ $sale->animal->tag_id }}</td>
                                <td class="text-right font-bold">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                <td class="text-right text-slate-500 italic">Rp {{ number_format($sale->final_hpp, 0, ',', '.') }}</td>
                                <td class="text-right font-black {{ ($sale->price - $sale->final_hpp) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($sale->price - $sale->final_hpp, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</x-print-layout>
