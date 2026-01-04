<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Tambah Ternak Baru</h2>
        <form action="{{ route('animals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="tag_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag ID (No. Telinga)</label>
                    <input type="text" id="tag_id" name="tag_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="partner_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mitra (Kepemilikan)</label>
                    <select id="partner_id" name="partner_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Pilih Mitra --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="breed_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ras (Breed)</label>
                    <select id="breed_id" name="breed_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="generation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Generasi (F1, F2, dst)</label>
                    <select id="generation" name="generation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Non-Genetik --</option>
                        <option value="F1">F1</option>
                        <option value="F2">F2</option>
                        <option value="F3">F3</option>
                        <option value="F4">F4</option>
                        <option value="F5">F5</option>
                        <option value="F6">F6</option>
                        <option value="PURE">Purebred</option>
                        <option value="CROSS">Crossbred</option>
                    </select>
                </div>
                <div>
                    <label for="gender" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="MALE">Jantan</option>
                        <option value="FEMALE">Betina</option>
                    </select>
                </div>
                <div>
                    <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                 <div>
                    <label for="current_location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang</label>
                    <select id="current_location_id" name="current_location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div>
                    <label for="current_phys_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Fisik</label>
                    <select id="current_phys_status_id" name="current_phys_status_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div>
                    <label for="necklace_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Warna Kalung</label>
                    <input type="text" id="necklace_color" name="necklace_color" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Perolehan</label>
                    <input type="text" value="Beli (Purchase)" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:text-white" disabled readonly>
                    <input type="hidden" name="acquisition_type" value="BOUGHT">
                </div>
                <div>
                    <label for="initial_weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Berat Awal (kg)</label>
                    <input type="number" id="initial_weight" name="initial_weight" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div id="purchase-price-container">
                    <label for="purchase_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga Beli (Rp)</label>
                    <input type="number" id="purchase_price" name="purchase_price" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div class="col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="photo">Upload Foto Hewan</label>
                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="photo" name="photo" type="file">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG or GIF (MAX. 20MB).</p>
                </div>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
        </form>
    </div>


</x-app-layout>
