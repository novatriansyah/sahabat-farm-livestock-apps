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
            <div class="print-subtitle">Laporan Operasional (Pakan): {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Operasional (Pakan)') }}</h2>
    </div>

    <!-- Filter -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4 dark:bg-gray-800 no-print">
        <form method="GET" action="{{ route('reports.operational') }}" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Bulan</label>
                <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tahun</label>
                <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @for($y=2023; $y<=date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Filter</button>
            <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Cetak (Print)</button>
        </form>
    </div>

    <!-- Summary -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-lg font-bold mb-4 text-orange-600 dark:text-orange-400">Ringkasan Biaya Pakan</h3>
            
            <div class="p-4 bg-orange-50 dark:bg-orange-900 rounded-lg mb-6">
                <p class="text-sm text-orange-600 dark:text-orange-200">Total Estimasi Biaya Pakan</p>
                <p class="text-3xl font-bold text-orange-800 dark:text-orange-100">Rp {{ number_format($totalCost, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-600 dark:text-orange-300 mt-1">*Berdasarkan rata-rata harga beli item pakan.</p>
            </div>

            <!-- Detail Table -->
            @if(empty($usageSummary))
                <p class="text-gray-500">Tidak ada penggunaan pakan bulan ini.</p>
            @else
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-8">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Nama Item</th>
                                <th class="px-6 py-3">Unit</th>
                                <th class="px-6 py-3 text-right">Qty Terpakai</th>
                                <th class="px-6 py-3 text-right">Harga Rata-rata/Unit</th>
                                <th class="px-6 py-3 text-right">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageSummary as $item)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $item['item_name'] }}</td>
                                <td class="px-6 py-4">{{ $item['unit'] }}</td>
                                <td class="px-6 py-4 text-right">{{ number_format($item['qty_used'], 1) }}</td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($item['avg_price'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-bold">Rp {{ number_format($item['total_cost'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- By Location -->
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Penggunaan per Kandang</h3>
             <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Lokasi (Kandang)</th>
                            <th class="px-6 py-3 text-right">Total Qty (Mix Unit)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locationSummary as $locName => $qty)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium">{{ $locName }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($qty, 1) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
