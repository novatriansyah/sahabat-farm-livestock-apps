<x-app-layout>
    <x-slot name="styles">
    <style>
        @media print {
            @page { size: A4; margin: 10mm; }
            body { font-family: sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; background: white; }
            
            /* Hide UI Elements */
            nav, header, aside, .no-print, form, button, #logo-sidebar { display: none !important; }
            
            /* Layout Adjustments */
            .min-h-screen { min-height: 0 !important; }
            .bg-gray-100 { background-color: white !important; }
            
            /* Main Content Wrapper */
            .p-4.sm\:ml-64 { margin-left: 0 !important; padding: 0 !important; width: 100% !important; border: none !important; }
            .p-4.mt-14 { padding: 0 !important; margin-top: 0 !important; }
            
            .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; }
            .rounded-lg, .rounded-md { border-radius: 0 !important; }
            
            /* Tables */
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
            
            /* Page Break */
            .page-break-before { page-break-before: always !important; display: block !important; }

            /* Header */
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
            
            /* Print Summary */
            .print-summary { display: flex !important; gap: 1rem; }
        }
        .print-header { display: none; }
        .print-summary { display: none; }
    </style>
    </x-slot>

    <!-- Print Header -->
    <div class="print-header">
        <img src="{{ asset('img/logo.png') }}" class="print-logo" alt="Logo">
        <div class="print-text">
            <div class="print-title">SAHABAT FARM INDONESIA</div>
            <div class="print-subtitle">Laporan Stok & Populasi: {{ date('F Y') }}</div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4 no-print">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Stok & Populasi') }}</h2>
        <a href="{{ route('reports.stock', array_merge(request()->all(), ['mode' => 'print'])) }}" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Cetak (Print All)</a>
    </div>

    <!-- Print Summary (Visible only in Print) -->
    <div class="print-summary mb-6">
        <div style="border: 1px solid #000; padding: 8px; text-align: center; flex: 1;">
            <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">Total Populasi</div>
            <div style="font-size: 14pt; font-weight: bold;">{{ $byGender['TOTAL'] }}</div>
        </div>
        <div style="border: 1px solid #000; padding: 8px; text-align: center; flex: 1;">
            <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">Total Jantan</div>
            <div style="font-size: 14pt; font-weight: bold;">{{ $byGender['MALE'] }}</div>
        </div>
        <div style="border: 1px solid #000; padding: 8px; text-align: center; flex: 1;">
            <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">Total Betina</div>
            <div style="font-size: 14pt; font-weight: bold;">{{ $byGender['FEMALE'] }}</div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6 no-print">
        <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg text-center">
            <p class="text-sm text-blue-600 dark:text-blue-200">Total Populasi</p>
            <p class="text-3xl font-bold text-blue-800 dark:text-blue-100">{{ $byGender['TOTAL'] }}</p>
        </div>
        <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg text-center">
            <p class="text-sm text-green-600 dark:text-green-200">Total Jantan</p>
            <p class="text-3xl font-bold text-green-800 dark:text-green-100">{{ $byGender['MALE'] }}</p>
        </div>
        <div class="p-4 bg-pink-50 dark:bg-pink-900 rounded-lg text-center">
            <p class="text-sm text-pink-600 dark:text-pink-200">Total Betina</p>
            <p class="text-3xl font-bold text-pink-800 dark:text-pink-100">{{ $byGender['FEMALE'] }}</p>
        </div>
    </div>

    <!-- Print Summary Section -->
    <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Rekapitulasi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-grid-2">
                <!-- By Location -->
                <div>
                    <h4 class="font-semibold mb-2">Populasi per Kandang</h4>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Lokasi</th>
                                <th class="px-4 py-2 text-center text-green-600">Jantan</th>
                                <th class="px-4 py-2 text-center text-pink-600">Betina</th>
                                <th class="px-4 py-2 text-center font-bold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byLocation as $locName => $stats)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-2 font-medium">{{ $locName }}</td>
                                <td class="px-4 py-2 text-center">{{ $stats['male'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $stats['female'] }}</td>
                                <td class="px-4 py-2 text-center font-bold">{{ $stats['total'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- By Breed -->
                <div>
                    <h4 class="font-semibold mb-2">Populasi per Breed</h4>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Breed</th>
                                <th class="px-4 py-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byBreed as $breedName => $count)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-2 font-medium">{{ $breedName }}</td>
                                <td class="px-4 py-2 text-right font-bold">{{ $count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Animal List -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800 page-break-before">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Detail Ternak Aktif</h3>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Tag ID</th>
                            <th class="px-6 py-3">Gender</th>
                            <th class="px-6 py-3">Breed</th>
                            <th class="px-6 py-3">Usia (Bulan)</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3">Status Fisik</th>
                            @if(Auth::user()->role === 'OWNER')
                            <th class="px-6 py-3">Mitra</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($animals as $animal)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $animal->tag_id }}</td>
                            <td class="px-6 py-4">{{ $animal->gender == 'MALE' ? 'Jantan' : 'Betina' }}</td>
                            <td class="px-6 py-4">{{ $animal->breed->name }}</td>
                            <td class="px-6 py-4">{{ number_format($animal->birth_date->diffInMonths(now()), 1) }} bln</td>
                            <td class="px-6 py-4">{{ $animal->location->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $animal->current_phys_status_id ?? '-' }}</td>
                            @if(Auth::user()->role === 'OWNER')
                            <td class="px-6 py-4">{{ $animal->partner->name ?? '-' }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(method_exists($animals, 'links'))
                    <div class="mt-4 no-print">
                        {{ $animals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(request('mode') === 'print')
        <script>
            window.onload = function() { window.print(); }
        </script>
    @endif
</x-app-layout>
