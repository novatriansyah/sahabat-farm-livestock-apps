<x-app-layout>
    <x-slot name="styles">
    <style>
        @media print {
            @page { size: A4; margin: 10mm; }
            body { font-family: sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white; }
            
            nav, header, aside, .no-print, form, button, #logo-sidebar { display: none !important; }
            .min-h-screen { min-height: 0 !important; }
            .bg-gray-100 { background-color: white !important; }
            .p-4.sm\:ml-64 { margin-left: 0 !important; padding: 0 !important; width: 100% !important; border: none !important; }
            .p-4.mt-14 { padding: 0 !important; margin-top: 0 !important; }
            
            table { width: 100% !important; border-collapse: collapse !important; font-size: 10pt; }
            th, td { border: 1px solid #000 !important; padding: 4px 8px !important; text-align: left; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
            
            .print-header { 
                display: flex !important; 
                align-items: center; 
                justify-content: center; 
                gap: 15px;
                margin-bottom: 20px; 
                border-bottom: 2px solid #000; 
                padding-bottom: 15px; 
            }
            .print-logo { height: 60px; width: auto; }
            .print-text { text-align: left; }
            .print-title { font-size: 18pt; font-weight: bold; line-height: 1.2; }
            .print-subtitle { font-size: 12pt; color: #000; }
        }
        .print-header { display: none; }
    </style>
    </x-slot>

    <!-- Print Header -->
    <div class="print-header">
        <img src="{{ asset('img/logo.png') }}" class="print-logo" alt="Logo">
        <div class="print-text">
            <div class="print-title">SAHABAT FARM INDONESIA</div>
            <div class="print-subtitle">Laporan Mitra Investigasi</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Mitra (Investor)') }}</h2>
    </div>

    <!-- Partner Selection (Owner Only) -->
    @if(Auth::user()->role === 'OWNER')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4 dark:bg-gray-800 no-print">
        <form method="GET" action="{{ route('reports.partners') }}" class="flex gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Mitra</label>
                <select name="partner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                    <option value="">-- Pilih Mitra --</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ $targetPartnerId == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Cetak (Print)</button>
        </form>
    </div>
    @endif

    @if(!$targetPartnerId)
        <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg dark:bg-yellow-900 dark:text-yellow-100">
            Silakan pilih Mitra untuk melihat laporan.
        </div>
    @else
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Hewan Aktif</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_animals'] }} Ekor</p>
            </div>
            <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg shadow-sm border border-blue-100">
                <p class="text-sm text-blue-600 dark:text-blue-200">Nilai Aset (Investasi Berjalan)</p>
                <p class="text-2xl font-bold text-blue-800 dark:text-blue-100">Rp {{ number_format($summary['asset_value'], 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg shadow-sm border border-green-100">
                <p class="text-sm text-green-600 dark:text-green-200">Total Profit Realisasi</p>
                <p class="text-2xl font-bold text-green-800 dark:text-green-100">Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}</p>
            </div>
            <div class="p-4 bg-purple-50 dark:bg-purple-900 rounded-lg shadow-sm border border-purple-100">
                <p class="text-sm text-purple-600 dark:text-purple-200">Total Omset Penjualan</p>
                <p class="text-2xl font-bold text-purple-800 dark:text-purple-100">Rp {{ number_format($summary['total_sales_revenue'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Active Assets Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-blue-600 dark:text-blue-400">Aset Ternak (Aktif)</h3>
                @if($activeAnimals->isEmpty())
                    <p class="text-gray-500">Mitra ini tidak memiliki ternak aktif.</p>
                @else
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Tag ID</th>
                                    <th class="px-4 py-2">Breed</th>
                                    <th class="px-4 py-2">Gender</th>
                                    <th class="px-4 py-2">Lokasi</th>
                                    <th class="px-4 py-2 text-right">Nilai Beli (Awal)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeAnimals as $animal)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-2 font-medium">{{ $animal->tag_id }}</td>
                                    <td class="px-4 py-2">{{ $animal->breed->name }}</td>
                                    <td class="px-4 py-2">{{ $animal->gender }}</td>
                                    <td class="px-4 py-2">{{ $animal->location->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($animal->purchase_price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sales History Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-green-600 dark:text-green-400">Riwayat Penjualan (Profit/Loss)</h3>
                @if($salesHistory->isEmpty())
                    <p class="text-gray-500">Belum ada penjualan untuk mitra ini.</p>
                @else
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Tgl Jual</th>
                                    <th class="px-4 py-2">Tag ID</th>
                                    <th class="px-4 py-2 text-right">Harga Jual</th>
                                    <th class="px-4 py-2 text-right">HPP Final</th>
                                    <th class="px-4 py-2 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesHistory as $sale)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $sale->exit_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 font-medium">{{ $sale->animal->tag_id }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($sale->final_hpp, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right font-bold {{ ($sale->price - $sale->final_hpp) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Rp {{ number_format($sale->price - $sale->final_hpp, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    @endif
</x-app-layout>
