<x-app-layout>

    <!-- Low Stock Alert -->
    @if(count($lowStockItems) > 0)
    <div class="mb-4 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
        <span class="font-medium">⚠️ Inventory Alert!</span> The following items are running low (< 10 units):
        <ul class="mt-1.5 list-disc list-inside">
            @foreach($lowStockItems as $item)
                <li>{{ $item->name }} (Current: {{ $item->current_stock }} {{ $item->unit }})</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Metric 1: Active Animals -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Live Population</h3>
                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $activeAnimals }}</span>
            </div>
        </div>

        <!-- Metric 2: Avg ADG -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Growth Performance (Avg ADG)</h3>
                <div class="flex items-center">
                     <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ number_format($avgAdg, 3) }} kg/day</span>
                     @if($avgAdg >= 0.15)
                        <span class="bg-green-100 text-green-800 text-xs font-medium ms-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Good</span>
                     @else
                        <span class="bg-red-100 text-red-800 text-xs font-medium ms-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Low</span>
                     @endif
                </div>
            </div>
        </div>

        <!-- Metric 3: Sales This Month -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Sales (This Month)</h3>
                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Metric 4: Net Profit -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Net Profit (This Month)</h3>
                <span class="text-2xl font-bold leading-none {{ $netProfit >= 0 ? 'text-green-500' : 'text-red-500' }} sm:text-3xl">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- Population by Cage -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Population by Cage</h3>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                @foreach($populationByCage as $cage)
                    <li class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span>{{ $cage->name }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $cage->animals_count }} Head</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Conception Rate & Charts -->
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Breeding Performance</h3>

            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 dark:text-gray-400">Conception Rate (All Time)</span>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($conceptionRate, 1) }}%</span>
            </div>

             <div class="relative h-48 w-full">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('performanceChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Asset Value (Million IDR)',
                    data: [12, 19, 3, 5, 2, 3],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>
