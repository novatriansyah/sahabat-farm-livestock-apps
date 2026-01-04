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
            /* Force Grid Side-by-Side in Print */
            .print-grid-2 { 
                display: grid !important; 
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important; 
                gap: 1.5rem !important; 
            }
            .page-break-inside-avoid { page-break-inside: avoid; }
            
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
            <div class="print-subtitle">Laporan Performa (ADG): {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Performa (ADG)') }}</h2>
    </div>

    <!-- Filter -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4 dark:bg-gray-800 no-print">
        <form method="GET" action="{{ route('reports.performance') }}" class="flex gap-4 items-end">
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

    @if($breedStats->isEmpty())
        <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg dark:bg-yellow-900 dark:text-yellow-100">
            Tidak ada data penimbangan pada periode ini. Silakan input bobot ternak terlebih dahulu.
        </div>
    @else
        
        <!-- Breed Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800 page-break-inside-avoid">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-blue-600 dark:text-blue-400">Rata-rata ADG per Breed</h3>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Breed</th>
                                <th class="px-6 py-3 text-center">Jumlah Sampel</th>
                                <th class="px-6 py-3 text-right">Rata-rata ADG</th>
                                <th class="px-6 py-3 text-right">Max ADG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($breedStats as $breed => $stat)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium">{{ $breed }}</td>
                                <td class="px-6 py-4 text-center">{{ $stat['count'] }}</td>
                                <td class="px-6 py-4 text-right font-bold text-blue-600">
                                    {{ number_format($stat['avg_adg'], 1) }} g/hari
                                </td>
                                <td class="px-6 py-4 text-right">{{ number_format($stat['max_adg'], 1) }} g</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-grid-2">
            <!-- Top Performers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800 page-break-inside-avoid">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4 text-green-600 dark:text-green-400">Top 10 Pertumbuhan Terbaik</h3>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-green-50 dark:bg-green-900 dark:text-green-300">
                                <tr>
                                    <th class="px-4 py-2">Tag ID</th>
                                    <th class="px-4 py-2">Breed</th>
                                    <th class="px-4 py-2 text-right">ADG (g)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $row)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-2 font-medium">{{ $row['animal']->tag_id }}</td>
                                    <td class="px-4 py-2">{{ $row['breed_name'] }}</td>
                                    <td class="px-4 py-2 text-right font-bold text-green-600">+{{ number_format($row['adg_grams'], 1) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Performers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4 text-red-600 dark:text-red-400">Perhatian (Pertumbuhan Rendah/Minus)</h3>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-red-50 dark:bg-red-900 dark:text-red-300">
                                <tr>
                                    <th class="px-4 py-2">Tag ID</th>
                                    <th class="px-4 py-2">Lokasi</th>
                                    <th class="px-4 py-2 text-right">ADG (g)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowPerformers as $row)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-4 py-2 font-medium">{{ $row['animal']->tag_id }}</td>
                                    <td class="px-4 py-2">{{ $row['location_name'] }}</td>
                                    <td class="px-4 py-2 text-right font-bold {{ $row['adg_grams'] < 0 ? 'text-red-600' : 'text-orange-500' }}">
                                        {{ number_format($row['adg_grams'], 1) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endif
</x-app-layout>
