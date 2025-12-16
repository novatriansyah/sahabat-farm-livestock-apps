<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">
                {{ __('Detail Mitra') }}: {{ $partner->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('partners.edit', $partner) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Edit Mitra
                </a>
                <a href="{{ route('partners.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Partner Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Informasi Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                            <p class="text-lg font-semibold">{{ $partner->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kontak / Alamat</p>
                            <p class="text-lg">{{ $partner->contact_info ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Hewan Dimiliki</p>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $partner->animals->count() }} Ekor</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bergabung Sejak</p>
                            <p class="text-lg">{{ $partner->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owned Animals Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Daftar Hewan Ternak (Aset)</h3>
                    @if($animals->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400">Mitra ini belum memiliki hewan ternak.</p>
                    @else
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tag ID</th>
                                        <th scope="col" class="px-6 py-3">Ras (Breed)</th>
                                        <th scope="col" class="px-6 py-3">Kelamin</th>
                                        <th scope="col" class="px-6 py-3">Lokasi</th>
                                        <th scope="col" class="px-6 py-3">HPP (Rp)</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animals as $animal)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $animal->tag_id }}</td>
                                        <td class="px-6 py-4">{{ $animal->breed->name ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $animal->gender == 'MALE' ? 'Jantan' : 'Betina' }}</td>
                                        <td class="px-6 py-4">{{ $animal->location->name ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ number_format($animal->current_hpp, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            @if($animal->is_active)
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Aktif</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('animals.show', $animal->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Lihat</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $animals->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
