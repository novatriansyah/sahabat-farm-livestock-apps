<x-app-layout>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Induk Menyusui') }}</h2>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Daftar Induk Menyusui & Cempe</h3>
            
            @if($nursingAnimals->count() > 0)
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Induk (Tag ID)</th>
                            <th class="px-6 py-3">Breed</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3">Cempe (Anak)</th>
                            <th class="px-6 py-3">Usia Cempe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nursingAnimals as $dam)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $dam->tag_id }}</td>
                            <td class="px-6 py-4">{{ $dam->breed->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $dam->location->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <ul class="list-disc list-inside">
                                    @forelse($dam->offspring as $kid)
                                        <li>{{ $kid->tag_id }} ({{ $kid->gender == 'JANTAN' ? 'J' : 'B' }})</li>
                                    @empty
                                        <span class="text-gray-400">-</span>
                                    @endforelse
                                </ul>
                            </td>
                            <td class="px-6 py-4">
                                @if($dam->offspring->count() > 0)
                                    @php $latestKid = $dam->offspring->first(); @endphp
                                    {{ number_format($latestKid->birth_date->diffInDays(now()), 0) }} Hari
                                    @if(number_format($latestKid->birth_date->diffInDays(now()), 0) >= 60)
                                        <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Siap Sapih</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500">Tidak ada induk dalam status menyusui saat ini.</p>
            @endif
        </div>
    </div>
</x-app-layout>
