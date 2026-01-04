<x-app-layout>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Manajemen Mitra') }}</h2>
        <a href="{{ route('partners.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">+ Tambah Mitra</a>
    </div>

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
                        @if(!$partner->user)
                            <a href="{{ route('users.create', ['role' => 'PARTNER', 'partner_id' => $partner->id]) }}" class="font-medium text-green-600 dark:text-green-500 hover:underline">Buat User</a>
                        @else
                            <span class="text-xs font-semibold px-2 py-1 rounded bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300">User Active</span>
                        @endif

                        <a href="{{ route('partners.show', $partner) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                        <a href="{{ route('partners.edit', $partner) }}" class="font-medium text-yellow-600 dark:text-yellow-500 hover:underline">Edit</a>
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
        <div class="p-4">
            {{ $partners->links() }}
        </div>
    </div>
</x-app-layout>
