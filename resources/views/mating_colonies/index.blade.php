<x-app-layout>
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Koloni Kawin</h2>
        <a href="{{ route('mating-colonies.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">+ Buat Koloni</a>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Koloni</th>
                    <th scope="col" class="px-6 py-3">Jantan (Pejantan)</th>
                    <th scope="col" class="px-6 py-3">Lokasi</th>
                    <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3">Jumlah Betina</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($colonies as $colony)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('mating-colonies.show', $colony->id) }}" class="text-blue-600 hover:underline">{{ $colony->name }}</a>
                    </td>
                    <td class="px-6 py-4">{{ $colony->sire->tag_id }} - {{ $colony->sire->breed->name }}</td>
                    <td class="px-6 py-4">{{ $colony->location->name }}</td>
                    <td class="px-6 py-4">{{ $colony->start_date->format('d M Y') }}</td>
                    <td class="px-6 py-4">{{ $colony->members->count() }}</td>
                    <td class="px-6 py-4">
                        @if($colony->status === 'ACTIVE')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Aktif</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Selesai</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $colonies->links() }}
        </div>
    </div>
</x-app-layout>
