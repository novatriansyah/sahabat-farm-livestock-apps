<x-app-layout>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Laporan Koloni Kawin') }}</h2>
    </div>

    <!-- Active Mating Colonies -->
    <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Koloni Kawin Aktif</h3>
            
            @if($colonies->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($colonies as $colony)
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="font-bold text-blue-600 dark:text-blue-400">{{ $colony->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $colony->location->name ?? 'No Location' }}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                            {{ $colony->members->count() }} Betina
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-semibold">Pejantan (Sire):</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $colony->sire->tag_id }} ({{ $colony->sire->breed->name ?? '-' }})</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold mb-1">Anggota Betina:</p>
                        <div class="max-h-32 overflow-y-auto">
                            <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-gray-700 uppercase bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-2 py-1">Tag ID</th>
                                        <th class="px-2 py-1">Breed</th>
                                        <th class="px-2 py-1">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($colony->members as $member)
                                    <tr>
                                        <td class="px-2 py-1 font-medium text-gray-900 dark:text-white">{{ $member->animal->tag_id }}</td>
                                        <td class="px-2 py-1">{{ $member->animal->breed->name ?? '-' }}</td>
                                        <td class="px-2 py-1">{{ $member->animal->physStatus->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500">Tidak ada koloni kawin aktif.</p>
            @endif
        </div>
    </div>

    <!-- Mating History / Active Events -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Jadwal Perkiraan Lahir (Menunggu)</h3>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Induk (Dam)</th>
                            <th class="px-6 py-3">Pejantan (Sire)</th>
                            <th class="px-6 py-3">Tgl Kawin</th>
                            <th class="px-6 py-3">Est. Lahir</th>
                            <th class="px-6 py-3">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeEvents as $event)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $event->dam->tag_id }}</td>
                            <td class="px-6 py-4">{{ $event->sire->tag_id }}</td>
                            <td class="px-6 py-4">{{ $event->mating_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold text-blue-600">{{ $event->est_birth_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                @php $days = now()->diffInDays($event->est_birth_date, false); @endphp
                                <span class="{{ number_format($days, 0) <= 7 ? 'text-red-600 font-bold' : '' }}">
                                    {{ number_format($days, 0) > 0 ? number_format($days, 0) . ' hari lagi' : 'Lewat ' . number_format(abs($days), 0) . ' hari' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
