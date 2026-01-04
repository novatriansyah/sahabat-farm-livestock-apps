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
            .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; }
            .rounded-lg, .rounded-md { border-radius: 0 !important; }
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
            <div class="print-subtitle">Laporan Penjualan: {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Penjualan') }}</h2>
    </div>

    <!-- Filter -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4 dark:bg-gray-800 no-print">
        <form method="GET" action="{{ route('reports.sales') }}" class="flex gap-4 items-end">
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

    <!-- Sales Report -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <!-- Financial Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                    <p class="text-sm text-blue-600 dark:text-blue-200">Total Omset (Revenue)</p>
                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-100">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                @if(Auth::user()->role === 'OWNER')
                <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                    <p class="text-sm text-green-600 dark:text-green-200">Estimasi Profit (Margin)</p>
                    <p class="text-2xl font-bold text-green-800 dark:text-green-100">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>

            @if($sales->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">Tidak ada penjualan bulan ini.</p>
            @else
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Tgl Jual</th>
                                <th class="px-6 py-3">Tag ID</th>
                                <th class="px-6 py-3">Breed</th>
                                <th class="px-6 py-3">Mitra</th>
                                <th class="px-6 py-3 whitespace-nowrap text-right">Harga Jual</th>
                                @if(Auth::user()->role === 'OWNER')
                                <th class="px-6 py-3 whitespace-nowrap text-right">HPP Final</th>
                                <th class="px-6 py-3 whitespace-nowrap text-right">Margin</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">{{ $sale->exit_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $sale->animal->tag_id }}</td>
                                <td class="px-6 py-4">{{ $sale->animal->breed->name }}</td>
                                <td class="px-6 py-4">{{ $sale->animal->partner->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                                @if(Auth::user()->role === 'OWNER')
                                <td class="px-6 py-4 text-right whitespace-nowrap">Rp {{ number_format($sale->final_hpp, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-bold whitespace-nowrap {{ ($sale->price - $sale->final_hpp) >= 0 ? 'text-green-600' : 'text-red-600' }}">
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
    </div>
</x-app-layout>
