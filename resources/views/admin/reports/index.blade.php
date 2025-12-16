<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Kelahiran & Kematian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" action="{{ route('reports.index') }}" class="flex gap-4 items-end">
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

            <!-- Births Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4 text-green-600 dark:text-green-400">Laporan Kelahiran</h3>
                    @if($births->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada data kelahiran bulan ini.</p>
                    @else
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Tgl Lahir</th>
                                        <th class="px-6 py-3">Tag ID</th>
                                        <th class="px-6 py-3">Gender</th>
                                        <th class="px-6 py-3">Breed (Gen)</th>
                                        <th class="px-6 py-3">Bobot Lahir</th>
                                        <th class="px-6 py-3">Indukan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($births as $birth)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">{{ $birth->birth_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $birth->tag_id }}</td>
                                        <td class="px-6 py-4">{{ $birth->gender == 'MALE' ? 'Jantan' : 'Betina' }}</td>
                                        <td class="px-6 py-4">{{ $birth->breed->name }} ({{ $birth->generation ?? '-' }})</td>
                                        <td class="px-6 py-4">{{ $birth->weightLogs->first()->weight_kg ?? '-' }} kg</td>
                                        <td class="px-6 py-4">
                                            Dam: {{ $birth->dam->tag_id ?? '-' }} <br>
                                            Sire: {{ $birth->sire->tag_id ?? '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Deaths Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4 text-red-600 dark:text-red-400">Laporan Kematian</h3>
                    @if($deaths->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada data kematian bulan ini.</p>
                    @else
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Tgl Mati</th>
                                        <th class="px-6 py-3">Tag ID</th>
                                        <th class="px-6 py-3">Gender</th>
                                        <th class="px-6 py-3">Breed</th>
                                        <th class="px-6 py-3">Usia Saat Mati</th>
                                        <th class="px-6 py-3">Nilai Kerugian (Est)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deaths as $death)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">{{ $death->exit_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $death->animal->tag_id }}</td>
                                        <td class="px-6 py-4">{{ $death->animal->gender == 'MALE' ? 'Jantan' : 'Betina' }}</td>
                                        <td class="px-6 py-4">{{ $death->animal->breed->name }}</td>
                                        <td class="px-6 py-4">{{ $death->animal->birth_date->diffInMonths($death->exit_date) }} bulan</td>
                                        <td class="px-6 py-4">Rp {{ number_format(($death->animal->purchase_price ?? 0) + $death->final_hpp, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
