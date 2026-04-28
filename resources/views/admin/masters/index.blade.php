<x-app-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Data Master</h2>
        <p class="text-gray-500 dark:text-gray-400">Mengelola konfigurasi peternakan (Bibit, Kandang, Penyakit, Item).</p>
    </div>


    <!-- Use Tabs for better organization -->
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="items-tab" data-tabs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="false">Inventory Items</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="breeds-tab" data-tabs-target="#breeds" type="button" role="tab" aria-controls="breeds" aria-selected="false">Bibit</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="locations-tab" data-tabs-target="#locations" type="button" role="tab" aria-controls="locations" aria-selected="false">Kandang</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="categories-tab" data-tabs-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="false">Jenis Hewan</button>
            </li>
            <li role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="diseases-tab" data-tabs-target="#diseases" type="button" role="tab" aria-controls="diseases" aria-selected="false">Penyakit</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="sops-tab" data-tabs-target="#sops" type="button" role="tab" aria-controls="sops" aria-selected="false">SOP Tugas</button>
            </li>
            <li role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="settings-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Pengaturan Farm</button>
            </li>
        </ul>
    </div>

    <div id="myTabContent">
        <!-- Inventory Items Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="items" role="tabpanel" aria-labelledby="items-tab">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Inventory Items</h3>
            </div>
            
            <form action="{{ route('masters.item.store') }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                @csrf
                <div class="grid gap-4 mb-4 md:grid-cols-4">
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Nama Item</label>
                        <input type="text" name="name" placeholder="e.g. Konsentrat" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Unit</label>
                        <input type="text" name="unit" placeholder="kg, ml, sak" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Kategori</label>
                        <select name="category" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">-- Pilih --</option>
                            <option value="Obat-Obatan">Obat-obatan</option>
                            <option value="Vitamin">Vitamin</option>
                            <option value="Vaksin">Vaksin</option>
                            <option value="Pakan">Pakan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Dosis (per kg)</label>
                        <input type="number" name="dosage_per_kg" placeholder="Optional" step="0.001" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">Tambah Item</button>
                </div>
            </form>

            <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama</th>
                            <th scope="col" class="px-6 py-3">Unit</th>
                            <th scope="col" class="px-6 py-3">Kategori</th>
                            <th scope="col" class="px-6 py-3">Dosis/kg</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($items as $item)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $item->name }}</th>
                            <td class="px-6 py-4">{{ $item->unit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ $item->category }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $item->dosage_per_kg ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('inventory.edit', $item->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ubah</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        </div>

        <!-- Breeds Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="breeds" role="tabpanel" aria-labelledby="breeds-tab">
            <h3 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">Bibit (Ras/Bangsa)</h3>
            <form action="{{ route('masters.breed.store') }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                @csrf
                <div class="grid gap-4 mb-4 md:grid-cols-4">
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Nama Bibit</label>
                        <input type="text" name="name" placeholder="e.g. Garut" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Jenis Hewan</label>
                        <select name="category_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Berat Min. (kg)</label>
                        <input type="number" name="min_weight_mate" placeholder="Prop. Kawin" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Umur Min. (Bulan)</label>
                        <input type="number" name="min_age_mate_months" placeholder="Prop. Kawin" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">Tambah Bibit</button>
                </div>
            </form>

            <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama</th>
                            <th scope="col" class="px-6 py-3">Jenis Hewan</th>
                            <th scope="col" class="px-6 py-3 text-center">Berat Min. Kawin</th>
                            <th scope="col" class="px-6 py-3 text-center">Umur Min. Kawin</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($breeds as $breed)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $breed->name }}</th>
                            <td class="px-6 py-4">{{ $breed->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ $breed->min_weight_mate ?? '-' }} kg</td>
                            <td class="px-6 py-4 text-center">{{ $breed->min_age_mate_months ?? '-' }} Bln</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('masters.breed.edit', $breed->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ubah</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $breeds->links() }}
            </div>
        </div>

        <!-- Locations Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="locations" role="tabpanel" aria-labelledby="locations-tab">
            <h3 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">Kandang</h3>
            <form action="{{ route('masters.location.store') }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                @csrf
                <div class="grid gap-4 mb-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Nama Kandang</label>
                        <input type="text" name="name" placeholder="e.g. Kandang A1" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Tipe</label>
                        <select name="type" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="Kandang Individu">Kandang Individu</option>
                            <option value="Kandang Koloni">Kandang Koloni</option>
                            <option value="Karantina">Karantina</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">Tambah Lokasi</button>
                </div>
            </form>

            <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Kandang</th>
                            <th scope="col" class="px-6 py-3">Tipe</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($locations as $loc)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $loc->name }}</th>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $loc->type === 'Karantina' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' }}">
                                    {{ $loc->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('masters.location.edit', $loc->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ubah</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $locations->links() }}
            </div>
        </div>

        <!-- Categories Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="categories" role="tabpanel" aria-labelledby="categories-tab">
            <h3 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">Jenis Hewan</h3>
            <form action="{{ route('masters.category.store') }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                @csrf
                <div class="flex gap-4">
                    <div class="flex-grow">
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Nama Jenis Hewan</label>
                        <input type="text" name="name" placeholder="e.g. Sapi, Domba, Kambing" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">Tambah</button>
                    </div>
                </div>
            </form>

            <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($categories as $cat)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $cat->name }}</th>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('masters.category.edit', $cat->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ubah</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>

        <!-- Diseases Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="diseases" role="tabpanel" aria-labelledby="diseases-tab">
            <h3 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">Penyakit</h3>
            <form action="{{ route('masters.disease.store') }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                @csrf
                <div class="grid gap-4 mb-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Nama Penyakit</label>
                        <input type="text" name="name" placeholder="e.g. Pink Eye" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Kategori</label>
                        <select name="category" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Pilih --</option>
                            <option value="Viral">Viral</option>
                            <option value="Bakteri">Bakteri</option>
                            <option value="Parasit">Parasit</option>
                            <option value="Pakan">Pakan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Gejala</label>
                        <textarea name="symptoms" placeholder="Ceritakan gejala yang umum terlihat..." class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" rows="2"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-xs font-semibold text-gray-500 uppercase">Rekomendasi Penanganan (Obat/Vitamin)</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                            @forelse($medicines as $item)
                                <div class="flex items-center justify-between p-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="treatments[]" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $item->name }}</label>
                                    </div>
                                    <input type="text" name="custom_dosages[{{ $item->id }}]" placeholder="Dosis Khusus (Opsional)" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-40 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-4 text-gray-500 italic text-sm">
                                    Belum ada data Obat/Vitamin di Inventori.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">Tambah Penyakit</button>
                </div>
            </form>

            <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Penyakit</th>
                            <th scope="col" class="px-6 py-3">Kategori</th>
                            <th scope="col" class="px-6 py-3">Gejala</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($diseases as $disease)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $disease->name }}</th>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $disease->category ?? 'Lainnya' }}</span>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate">{{ $disease->symptoms ?? '-' }}</td>
                            <td class="px-6 py-4 text-right text-black">
                                <a href="{{ route('masters.disease.edit', $disease->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Ubah</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $diseases->links() }}
            </div>
        </div>

        <!-- SOP Tasks Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="sops" role="tabpanel" aria-labelledby="sops-tab">
            <h3 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">Standar Operasional Prosedur (Tugas Otomatis)</h3>
            <form action="{{ route('masters.sop.store') }}" method="POST" class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                @csrf
                <div class="grid gap-4 mb-4 md:grid-cols-4">
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Event Pemicu</label>
                        <select name="event_type" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="BIRTH">Kelahiran (Birth)</option>
                            <option value="ARRIVAL">Kedatangan (Arrival)</option>
                            <option value="ROUTINE">Rutin (Routine)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Judul Tugas</label>
                        <input type="text" name="title" placeholder="e.g. Pemberian Vitamin A" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Tipe Tugas</label>
                        <select name="task_type" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="HEALTH">Kesehatan</option>
                            <option value="ROUTINE">Rutin</option>
                            <option value="ARRIVAL">Kedatangan</option>
                            <option value="GENERAL">Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase">Offset Hari (H+)</label>
                        <input type="number" name="due_days_offset" value="0" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">Tambah SOP</button>
                </div>
            </form>

            <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Event</th>
                            <th scope="col" class="px-6 py-3">Judul Tugas</th>
                            <th scope="col" class="px-6 py-3">Tipe</th>
                            <th scope="col" class="px-6 py-3 text-center">Offset (Hari)</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($sops as $sop)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $sop->event_type }}</td>
                            <td class="px-6 py-4">{{ $sop->title }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">{{ $sop->task_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">H+{{ $sop->due_days_offset }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('masters.sop.destroy', $sop->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline font-medium" onclick="return confirm('Hapus SOP ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $sops->links() }}
            </div>
        </div>

        <!-- Farm Settings Section -->
        <div class="hidden p-6 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700" id="settings" role="tabpanel" aria-labelledby="settings-tab">
            <h3 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">Konfigurasi Parameter Peternakan</h3>
            
            <form action="{{ route('masters.settings.update') }}" method="POST">
                @csrf
                @foreach($settings as $group => $items)
                    <div class="mb-8">
                        <h4 class="mb-4 text-sm font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ $group }}</h4>
                        <div class="grid gap-6 mb-4 md:grid-cols-2">
                            @foreach($items as $setting)
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $setting->label }}</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                                        @if(str_contains($setting->key, 'days'))
                                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 whitespace-nowrap">Hari</span>
                                        @elseif(str_contains($setting->key, 'cost'))
                                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 whitespace-nowrap">Rp</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-10 py-3 text-center transition shadow-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
