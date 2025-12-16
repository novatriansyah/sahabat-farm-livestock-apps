<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Mitra') }}
            </h2>
            <a href="{{ route('partners.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                + Tambah Mitra
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Mitra</th>
                                    <th scope="col" class="px-6 py-3">Kontak</th>
                                    <th scope="col" class="px-6 py-3">Jumlah Ternak</th>
                                    <th scope="col" class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partners as $partner)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $partner->name }}</td>
                                    <td class="px-6 py-4">{{ $partner->contact_info ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $partner->animals_count ?? $partner->animals()->count() }}</td>
                                    <td class="px-6 py-4 flex space-x-2">
                                        <a href="{{ route('partners.edit', $partner) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                        <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus mitra ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $partners->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
