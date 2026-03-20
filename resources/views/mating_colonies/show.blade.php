<x-app-layout>
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detail Koloni: {{ $matingColony->name }}</h2>
            <a href="{{ route('mating-colonies.index') }}" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">← Kembali ke daftar</a>
        </div>
        @if($matingColony->status === 'ACTIVE')
        <form action="{{ route('mating-colonies.update', $matingColony->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan koloni ini (semua betina akan ditandai selesai kawin)?');">
            @csrf
            @method('PUT')
            <input type="hidden" name="complete" value="1">
            <button type="submit" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">Selesaikan Koloni</button>
        </form>
        @endif
    </div>

    <!-- Details -->
    <div class="bg-white p-6 rounded-lg shadow mb-6 dark:bg-gray-800">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $matingColony->status === 'ACTIVE' ? 'Aktif' : 'Selesai' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Mulai</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $matingColony->start_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Pejantan</p>
                <p class="font-bold text-gray-900 dark:text-white">
                    <a href="{{ route('animals.show', $matingColony->sire_id) }}" class="text-blue-600 hover:underline">{{ $matingColony->sire->tag_id }}</a>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Lokasi</p>
                <p class="font-bold text-gray-900 dark:text-white">{{ $matingColony->location->name }}</p>
            </div>
        </div>
    </div>

    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Daftar Indukan (Betina)</h3>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tag ID</th>
                    <th scope="col" class="px-6 py-3">Ras</th>
                    <th scope="col" class="px-6 py-3">Tanggal Bergabung</th>
                    <th scope="col" class="px-6 py-3">Status Kawin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matingColony->members as $member)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('animals.show', $member->dam_id) }}" class="text-blue-600 hover:underline">{{ $member->dam->tag_id }}</a>
                    </td>
                    <td class="px-6 py-4">{{ $member->dam->breed->name }}</td>
                    <td class="px-6 py-4">{{ $member->joined_date->format('d M Y') }}</td>
                    <td class="px-6 py-4">{{ $member->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
