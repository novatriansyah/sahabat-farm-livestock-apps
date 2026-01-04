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
            <div class="print-subtitle">Audit Internal (Efisiensi Kandang): {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Audit Internal (Efisiensi Kandang)') }}</h2>
        <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Cetak (Print)</button>
    </div>

    <!-- Filter -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4 dark:bg-gray-800 no-print">
        <form method="GET" action="{{ route('reports.audit') }}" class="flex gap-4 items-end">
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
        </form>
    </div>

    @if(empty($auditData))
        <div class="p-4 bg-gray-50 text-gray-500 rounded-lg dark:bg-gray-900 dark:text-gray-400">
            Tidak ada aktivitas (kelahiran/kematian) atau populasi pada periode ini.
        </div>
    @else
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-red-600 dark:text-red-400">Analisa Mortalitas per Lokasi</h3>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Lokasi (Kandang)</th>
                                <th class="px-6 py-3 text-center">Populasi Aktif</th>
                                <th class="px-6 py-3 text-center">Kelahiran</th>
                                <th class="px-6 py-3 text-center">Kematian</th>
                                <th class="px-6 py-3 text-right">Tingkat Kematian (Mortality Rate)</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditData as $row)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium">{{ $row['location'] }}</td>
                                <td class="px-6 py-4 text-center">{{ $row['population'] }}</td>
                                <td class="px-6 py-4 text-center text-green-600 font-bold">+{{ $row['births'] }}</td>
                                <td class="px-6 py-4 text-center text-red-600 font-bold">{{ $row['deaths'] > 0 ? '-'.$row['deaths'] : '0' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold {{ $row['mortality_rate'] > 5 ? 'text-red-600' : ($row['mortality_rate'] > 2 ? 'text-orange-500' : 'text-green-600') }}">
                                        {{ number_format($row['mortality_rate'], 2) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($row['mortality_rate'] > 5)
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-bold">KRITIS</span>
                                    @elseif($row['mortality_rate'] > 2)
                                        <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-bold">WASPADA</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-bold">AMAN</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
