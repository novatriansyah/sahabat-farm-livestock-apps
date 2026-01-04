<x-app-layout>
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Data Ternak</h2>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('animals.index') }}" class="w-full md:w-1/3">
                <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="search" name="search" value="{{ request('search') }}" class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari Tag ID atau Ras..." />
                </div>
            </form>

            <div class="flex flex-wrap gap-2">
                <!-- Import Excel Button -->
                <button data-modal-target="import-modal" data-modal-toggle="import-modal" class="text-white bg-teal-600 hover:bg-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                    Upload Excel
                </button>
                
                <a href="{{ route('birth.create') }}" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">+ Kelahiran</a>
                <a href="{{ route('animals.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">+ Ternak Baru</a>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="import-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Upload Data Ternak
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="import-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form action="{{ route('animals.import') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                    @csrf
                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx, .xls, .csv" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
                    </div>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('animals.template') }}" class="text-sm text-blue-600 hover:underline dark:text-blue-500">Download Template</a>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tag ID</th>
                    <th scope="col" class="px-6 py-3">Foto</th>
                    <th scope="col" class="px-6 py-3">Ras (Breed)</th>
                    <th scope="col" class="px-6 py-3">Kelamin</th>
                    <th scope="col" class="px-6 py-3">Status Kesehatan</th>
                    <th scope="col" class="px-6 py-3">Status Fisik</th>
                    <th scope="col" class="px-6 py-3">Warna Eartag</th>
                    <th scope="col" class="px-6 py-3">Warna Kalung</th>
                    <th scope="col" class="px-6 py-3">Lokasi</th>
                    <th scope="col" class="px-6 py-3">ADG (kg/hari)</th>
                    <th scope="col" class="px-6 py-3">HPP (Rp)</th>
                    @if(auth()->user()->role !== 'PARTNER')
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($animals as $animal)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('animals.show', $animal->id) }}" class="hover:underline text-blue-600">
                            {{ $animal->tag_id }}
                        </a>
                        @if($animal->generation)
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium ml-1 px-1.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ $animal->generation }}</span>
                        @endif
                    </th>
                    <td class="px-6 py-4">
                        @if($animal->photos->count() > 0)
                            <img class="w-10 h-10 rounded-full" src="{{ Storage::url($animal->photos->first()->photo_url) }}" alt="Animal Photo">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $animal->breed->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $animal->gender == 'MALE' ? 'Jantan' : 'Betina' }}</td>
                    <td class="px-6 py-4">
                        @if($animal->health_status == 'HEALTHY')
                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Sehat</span>
                        @elseif($animal->health_status == 'SICK')
                            <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Sakit</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{ $animal->health_status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $animal->physStatus->name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $animal->ear_tag_color ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $animal->necklace_color ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $animal->location->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($animal->daily_adg < 0.1)
                            <span class="text-red-600 font-bold dark:text-red-500">{{ number_format($animal->daily_adg, 3) }} ▼</span>
                        @else
                            <span class="text-green-600 dark:text-green-500">{{ number_format($animal->daily_adg, 3) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ number_format($animal->current_hpp, 0, ',', '.') }}</td>
                    @if(auth()->user()->role !== 'PARTNER')
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('animals.print', $animal->id) }}" target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">QR</a>
                            @if($animal->gender === 'FEMALE' && $animal->is_active)
                                <a href="{{ route('breeding.create', $animal->id) }}" class="font-medium text-purple-600 dark:text-purple-500 hover:underline">Kawin</a>
                            @endif
                            @if($animal->is_active)
                                <a href="{{ route('animals.exit.create', $animal->id) }}" class="font-medium text-red-600 dark:text-red-500 hover:underline">Keluar</a>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $animals->links() }}
        </div>
    </div>
</x-app-layout>
