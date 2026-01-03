<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Partner Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Stat 1: Live Animals -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $activeAnimals }} Ekor</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Total Populasi Hidup</p>
                    <p class="text-xs text-gray-500 mt-2">Jantan: {{ $liveMale }} | Betina: {{ $liveFemale }}</p>
                </div>

                <!-- Stat 2: Sales -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Penjualan (Bulan Ini)</p>
                </div>

                <!-- Stat 3: Est. Profit -->
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Estimasi Profit (Bulan Ini)</p>
                </div>
            </div>

            <!-- Recent Animals Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Hewan Terbaru</h3>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Tag ID</th>
                                    <th scope="col" class="px-6 py-3">Kategori</th>
                                    <th scope="col" class="px-6 py-3">Gender</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Tanggal Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAnimals as $animal)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $animal->tag_id }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $animal->category->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $animal->gender }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $animal->physStatus->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($animal->entry_date)->format('d M Y') }}
                                    </td>
                                </tr>
                                @endforeach
                                @if($recentAnimals->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">Belum ada data hewan.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
