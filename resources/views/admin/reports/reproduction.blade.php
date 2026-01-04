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
            <div class="print-subtitle">Laporan Reproduksi (Indukan)</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Reproduksi') }}</h2>
        <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Cetak (Print)</button>
    </div>

    @if($reproData->isEmpty())
        <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg dark:bg-yellow-900 dark:text-yellow-100">
            Belum ada data kelahiran dari indukan yang tercatat.
        </div>
    @else
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
            <div class="p-6">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Indukan (Tag ID)</th>
                                <th class="px-6 py-3">Breed</th>
                                <th class="px-6 py-3 text-center">Total Kelahiran (Kali)</th>
                                <th class="px-6 py-3 text-center">Total Anak (Ekor)</th>
                                <th class="px-6 py-3 text-center">Rata2 Anak/Lahir</th>
                                <th class="px-6 py-3 text-center">Interval (Hari)</th>
                                <th class="px-6 py-3 text-right">Kelahiran Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reproData as $row)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $row['dam']->tag_id }}</td>
                                <td class="px-6 py-4">{{ $row['dam']->breed->name }}</td>
                                <td class="px-6 py-4 text-center">{{ $row['total_litters'] }}</td>
                                <td class="px-6 py-4 text-center">{{ $row['total_offspring'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded-full {{ $row['avg_litter_size'] >= 2 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ number_format($row['avg_litter_size'], 1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($row['avg_interval_days'] > 0)
                                        {{ number_format($row['avg_interval_days'], 0) }} hari
                                        <br>
                                        <span class="text-xs text-gray-500">({{ number_format($row['avg_interval_days']/30, 1) }} bulan)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ $row['last_birth_date'] ? $row['last_birth_date']->format('d/m/Y') : '-' }} <br>
                                    <span class="text-xs {{ $row['days_since_last_birth'] > 240 ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                        {{ $row['days_since_last_birth'] }} hari lalu
                                    </span>
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
