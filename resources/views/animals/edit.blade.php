<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Edit Data Ternak: {{ $animal->tag_id }}</h2>
        <form action="{{ route('animals.update', $animal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="tag_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag ID (No. Telinga)</label>
                    <input type="text" id="tag_id" name="tag_id" value="{{ old('tag_id', $animal->tag_id) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="partner_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mitra (Kepemilikan)</label>
                    <select id="partner_id" name="partner_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Pilih Mitra --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ $animal->partner_id == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $animal->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="breed_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ras (Breed)</label>
                    <select id="breed_id" name="breed_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}" {{ $animal->breed_id == $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="generation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Generasi</label>
                    <select id="generation" name="generation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Non-Genetik --</option>
                        @foreach(['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'PURE', 'CROSS'] as $gen)
                            <option value="{{ $gen }}" {{ $animal->generation == $gen ? 'selected' : '' }}>{{ $gen }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="gender" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="MALE" {{ $animal->gender == 'MALE' ? 'selected' : '' }}>Jantan</option>
                        <option value="FEMALE" {{ $animal->gender == 'FEMALE' ? 'selected' : '' }}>Betina</option>
                    </select>
                </div>
                <div>
                    <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $animal->birth_date->format('Y-m-d')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                 <div>
                    <label for="current_location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang</label>
                    <select id="current_location_id" name="current_location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $animal->current_location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div>
                    <label for="current_phys_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Fisik</label>
                    <select id="current_phys_status_id" name="current_phys_status_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $animal->current_phys_status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div>
                    <label for="necklace_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Warna Kalung</label>
                    <input type="text" id="necklace_color" name="necklace_color" value="{{ old('necklace_color', $animal->necklace_color) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="photo">Update Foto</label>
                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="photo" name="photo" type="file">
                </div>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Perubahan</button>
        </form>
    </div>
</x-app-layout>
