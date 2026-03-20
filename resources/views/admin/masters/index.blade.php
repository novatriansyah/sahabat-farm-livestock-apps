<x-app-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Data Master</h2>
        <p class="text-gray-500 dark:text-gray-400">Mengelola konfigurasi peternakan (Bibit, Kandang, Penyakit, Item).</p>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

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
        </ul>
    </div>

    <div id="myTabContent">
        <!-- Inventory Items Section -->
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="items" role="tabpanel" aria-labelledby="items-tab">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Inventory Items</h3>
            <form action="{{ route('masters.item.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Nama Item" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <input type="text" name="unit" placeholder="Unit (kg, ml, sak)" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <select name="category" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Obat-Obatan">Obat-obatan</option>
                        <option value="Vitamin">Vitamin</option>
                        <option value="Vaksin">Vaksin</option>
                        <option value="Pakan">Pakan</option>
                    </select>
                    <input type="number" name="dosage_per_kg" placeholder="Dosis (per kg) - Optional" step="0.001" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Tambah Item</button>
            </form>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($items as $item)
                    <li>{{ $item->name }} ({{ $item->category }}) <a href="{{ route('inventory.edit', $item->id) }}" class="text-blue-500 hover:underline text-xs ml-2">Ubah</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Breeds Section -->
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="breeds" role="tabpanel" aria-labelledby="breeds-tab">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Bibit</h3>
            <form action="{{ route('masters.breed.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Nama Bibit" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <select name="category_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="min_weight_mate" placeholder="Berat Minimum (kg)" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <input type="number" name="min_age_mate_months" placeholder="Umur Minimum (Bulan)" min="0" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Tambah Bibit</button>
            </form>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($breeds as $breed)
                    <li>{{ $breed->name }} ({{ $breed->category->name ?? '-' }}) <a href="{{ route('masters.breed.edit', $breed->id) }}" class="text-blue-500 hover:underline text-xs ml-2">Ubah</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Locations Section -->
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="locations" role="tabpanel" aria-labelledby="locations-tab">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Kandang</h3>
            <form action="{{ route('masters.location.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Lokasi Kandang" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <select name="type" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="Kandang Individu">Kandang Individu</option>
                        <option value="Kandang Koloni">Kandang Koloni</option>
                        <option value="Karantina">Karantina</option>
                    </select>
                </div>
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700">Tambah Lokasi Kandang</button>
            </form>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($locations as $loc)
                    <li>{{ $loc->name }} ({{ $loc->type }}) <a href="{{ route('masters.location.edit', $loc->id) }}" class="text-blue-500 hover:underline text-xs ml-2">Ubah</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Categories Section -->
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="categories" role="tabpanel" aria-labelledby="categories-tab">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Jenis Hewan</h3>
            <form action="{{ route('masters.category.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Jenis Hewan (e.g. Sapi)" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700">Jenis Hewan</button>
            </form>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($categories as $cat)
                    <li>{{ $cat->name }} <a href="{{ route('masters.category.edit', $cat->id) }}" class="text-blue-500 hover:underline text-xs ml-2">Ubah</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Diseases Section -->
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="diseases" role="tabpanel" aria-labelledby="diseases-tab">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Penyakit</h3>
            <form action="{{ route('masters.disease.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Nama Penyakit" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <input type="text" name="symptoms" placeholder="Gejala" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700">Tambah Penyakit</button>
            </form>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($diseases as $disease)
                    <li>{{ $disease->name }} <a href="{{ route('masters.disease.edit', $disease->id) }}" class="text-blue-500 hover:underline text-xs ml-2">Ubah</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</x-app-layout>
