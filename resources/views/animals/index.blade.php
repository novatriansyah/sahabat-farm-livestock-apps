<x-app-layout>
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Data Ternak</h2>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700 mb-6">
            <form method="GET" action="{{ route('animals.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Text Search -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold uppercase text-gray-500">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag ID / Ras..." class="w-full text-sm border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <!-- Breed Filter -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold uppercase text-gray-500">Jenis & Ras</label>
                        <select name="breed_id" class="w-full text-sm border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Ras</option>
                            @foreach($breeds as $breed)
                                <option value="{{ $breed->id }}" {{ request('breed_id') == $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold uppercase text-gray-500">Kelamin</label>
                        <select name="gender" class="w-full text-sm border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Kelamin</option>
                            <option value="JANTAN" {{ request('gender') === 'JANTAN' ? 'selected' : '' }}>Jantan</option>
                            <option value="BETINA" {{ request('gender') === 'BETINA' ? 'selected' : '' }}>Betina</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold uppercase text-gray-500">Status Fisik</label>
                        <select name="phys_status_id" class="w-full text-sm border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('phys_status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold uppercase text-gray-500">Lokasi</label>
                        <select name="location_id" class="w-full text-sm border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(auth()->user()->role === 'PEMILIK')
                    <!-- Partner Filter -->
                    <div>
                        <label class="block mb-1 text-xs font-semibold uppercase text-gray-500">Mitra/Pemilik</label>
                        <select name="partner_id" class="w-full text-sm border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Semua Mitra</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-2">
                    <div class="flex gap-2">
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('animals.index') }}" class="px-5 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            Reset
                        </a>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" data-modal-target="import-modal" data-modal-toggle="import-modal" class="px-5 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            Unggah Excel
                        </button>
                        <a href="{{ route('birth.create') }}" class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">+ Kelahiran</a>
                        <a href="{{ route('animals.create') }}" class="px-5 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800">+ Ternak Baru</a>
                    </div>
                </div>
            </form>
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
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">Jenis & Ras</th>
                    <th scope="col" class="px-6 py-3">Kelamin</th>
                    <th scope="col" class="px-6 py-3">Usia Hewan</th>
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">Status Kesehatan</th>
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">Status Fisik</th>
                    <th scope="col" class="px-6 py-3 hidden lg:table-cell">Warna Eartag</th>
                    <th scope="col" class="px-6 py-3 hidden lg:table-cell">Warna Kalung</th>
                    <th scope="col" class="px-6 py-3 hidden md:table-cell">Lokasi</th>
                    <th scope="col" class="px-6 py-3">BB (kg)</th>
                    <th scope="col" class="px-6 py-3">Kepemilikan</th>
                    <th scope="col" class="px-6 py-3">ADG (kg/hari)</th>
                    <th scope="col" class="px-6 py-3 hidden sm:table-cell">HPP (Rp)</th>
                    @if(auth()->user()->role !== 'MITRA')
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
                    </th>
                    <td class="px-6 py-4">
                        @if($animal->photos->count() > 0)
                            <div x-data="{ open: false, index: 0, photos: {{ json_encode($animal->photos->map(fn($p) => Storage::url($p->photo_url))) }} }">
                                <img @click="open = true" class="w-10 h-10 rounded-full cursor-pointer hover:opacity-75 transition-opacity" src="{{ Storage::url($animal->photos->first()->photo_url) }}" alt="Animal Photo">
                                
                                <!-- Lightbox Modal -->
                                <template x-teleport="body">
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90 p-4"
                                         @keydown.escape.window="open = false">
                                        
                                        <button @click="open = false" class="absolute top-5 right-5 text-white hover:text-gray-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>

                                        <div class="relative max-w-4xl w-full flex items-center justify-center">
                                            <button x-show="photos.length > 1" @click="index = (index - 1 + photos.length) % photos.length" class="absolute left-0 text-white p-2 hover:bg-white/10 rounded-full transition-colors">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                            </button>

                                            <img :src="photos[index]" class="max-h-[85vh] max-w-full object-contain shadow-2xl rounded-lg">

                                            <button x-show="photos.length > 1" @click="index = (index + 1) % photos.length" class="absolute right-0 text-white p-2 hover:bg-white/10 rounded-full transition-colors">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </button>
                                        </div>

                                        <div class="absolute bottom-5 text-white font-medium bg-black/50 px-4 py-2 rounded-full backdrop-blur-sm">
                                            <span x-text="index + 1"></span> / <span x-text="photos.length"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">{{ $animal->full_breed }}</td>
                    <td class="px-6 py-4">{{ $animal->gender == 'JANTAN' ? 'Jantan' : 'Betina' }}</td>
                    <td class="px-6 py-4">{{ $animal->age_string }}</td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        @if($animal->health_status == 'SEHAT')
                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Sehat</span>
                        @elseif($animal->health_status == 'SAKIT')
                            <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Sakit</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{ $animal->health_status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $animal->physStatus->name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">{{ $animal->ear_tag_color ?? '-' }}</td>
                    <td class="px-6 py-4 hidden lg:table-cell">{{ $animal->necklace_color ?? '-' }}</td>
                    <td class="px-6 py-4 hidden md:table-cell">{{ $animal->location->name ?? '-' }}</td>
                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $animal->latestWeightLog->weight_kg ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs font-semibold">{{ $animal->partner->name ?? 'Internal SFI' }}</td>
                    <td class="px-6 py-4">
                        @if($animal->daily_adg < 0.1)
                            <span class="text-red-600 font-bold dark:text-red-500">{{ number_format($animal->daily_adg, 3) }} ▼</span>
                        @else
                            <span class="text-green-600 dark:text-green-500">{{ number_format($animal->daily_adg, 3) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden sm:table-cell">{{ number_format($animal->current_hpp, 0, ',', '.') }}</td>
                    @if(auth()->user()->role !== 'MITRA')
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('animals.edit', $animal->id) }}" class="font-medium text-yellow-600 dark:text-yellow-500 hover:underline">Edit</a>
                            <a href="{{ route('animals.print', $animal->id) }}" target="_blank" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">QR</a>
                            @if($animal->gender === 'BETINA' && $animal->is_active)
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
