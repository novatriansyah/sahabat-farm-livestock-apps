<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $item->name }}</h2>
            <a href="{{ route('inventory.edit', $item->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Edit Item</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center pb-10">
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $item->current_stock }} {{ $item->unit }}</h5>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Current Stock</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Category:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->category }}</span>
                    </div>
                    @if($item->dosage_per_kg)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dosage Rule:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->dosage_per_kg }} / kg</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="usage-tab" data-tabs-target="#usage" type="button" role="tab" aria-controls="usage" aria-selected="false">Usage History</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="purchases-tab" data-tabs-target="#purchases" type="button" role="tab" aria-controls="purchases" aria-selected="false">Purchase History</button>
                        </li>
                    </ul>
                </div>
                <div id="myTabContent">
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="usage" role="tabpanel" aria-labelledby="usage-tab">
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Date</th>
                                        <th scope="col" class="px-6 py-3">Used</th>
                                        <th scope="col" class="px-6 py-3">Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->usageLogs()->orderByDesc('usage_date')->take(10)->get() as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $log->usage_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-red-600">-{{ $log->qty_used + $log->qty_wasted }} {{ $item->unit }}</td>
                                        <td class="px-6 py-4">{{ $log->location->name ?? 'General' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Date</th>
                                        <th scope="col" class="px-6 py-3">Qty</th>
                                        <th scope="col" class="px-6 py-3">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->purchases()->orderByDesc('date')->take(10)->get() as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $log->date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-green-600">+{{ $log->qty }} {{ $item->unit }}</td>
                                        <td class="px-6 py-4">Rp {{ number_format($log->price_total, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
