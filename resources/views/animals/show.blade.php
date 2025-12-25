<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Ternak</h2>
            <div class="flex gap-2">
                <a href="{{ route('animals.edit', $animal->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Edit</a>
                <a href="{{ route('animals.print', $animal->id) }}" target="_blank" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cetak QR</a>
                @if($animal->is_active)
                    <a href="{{ route('animals.exit.create', $animal->id) }}" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">Exit (Jual/Mati)</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center pb-10">
                    @if($animal->photos->count() > 0)
                        <img class="w-24 h-24 mb-3 rounded-full shadow-lg object-cover" src="{{ Storage::url($animal->photos->last()->photo_url) }}" alt="Animal Photo"/>
                    @else
                        <div class="w-24 h-24 mb-3 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $animal->tag_id }}</h5>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $animal->breed->name }} ({{ $animal->gender == 'MALE' ? 'Jantan' : 'Betina' }})</span>

                    <div class="mt-4 flex space-x-3 md:mt-6">
                        @if($animal->health_status == 'HEALTHY')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Sehat</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $animal->health_status }}</span>
                        @endif
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $animal->physStatus->name }}</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $animal->location->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Umur:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $animal->birth_date->diffForHumans(null, true) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemilik / Mitra:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $animal->partner->name ?? ($animal->owner->name ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">HPP Saat Ini:</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($animal->current_hpp, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabs / History -->
            <div class="md:col-span-2">
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="weight-tab" data-tabs-target="#weight" type="button" role="tab" aria-controls="weight" aria-selected="false">Riwayat Bobot</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="health-tab" data-tabs-target="#health" type="button" role="tab" aria-controls="health" aria-selected="false">Riwayat Kesehatan</button>
                        </li>
                    </ul>
                </div>
                <div id="myTabContent">
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="weight" role="tabpanel" aria-labelledby="weight-tab">
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tanggal</th>
                                        <th scope="col" class="px-6 py-3">Bobot (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animal->weightLogs()->orderByDesc('weigh_date')->get() as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $log->weigh_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 font-bold">{{ $log->weight_kg }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="health" role="tabpanel" aria-labelledby="health-tab">
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tanggal</th>
                                        <th scope="col" class="px-6 py-3">Tipe</th>
                                        <th scope="col" class="px-6 py-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animal->treatmentLogs()->orderByDesc('treatment_date')->get() as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $log->treatment_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $log->type }}</span>
                                        </td>
                                        <td class="px-6 py-4">{{ $log->notes }}</td>
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
