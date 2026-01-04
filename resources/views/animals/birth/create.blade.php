<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Registrasi Kelahiran (Recording Birth)</h2>
        <form action="{{ route('birth.store') }}" method="POST">
            @csrf

            <!-- Parent Info -->
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Indukan (Parents)</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="dam_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Induk Betina (Dam)</label>
                        <select id="dam_id" name="dam_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">-- Pilih Induk --</option>
                            @foreach($dams as $dam)
                                <option value="{{ $dam->id }}">{{ $dam->tag_id }} ({{ $dam->breed->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sire_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pejantan (Sire)</label>
                        <select id="sire_id" name="sire_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Pilih Pejantan (Opsional) --</option>
                            @foreach($sires as $sire)
                                <option value="{{ $sire->id }}">{{ $sire->tag_id }} ({{ $sire->breed->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Offspring Info -->
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Anak (Cempe)</h3>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="tag_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag ID (No. Telinga)</label>
                    <input type="text" id="tag_id" name="tag_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="gender" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="MALE">Jantan</option>
                        <option value="FEMALE">Betina</option>
                    </select>
                </div>
                <div>
                    <label for="initial_weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Berat Lahir (kg)</label>
                    <input type="number" id="initial_weight" name="initial_weight" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
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
                    <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                    <label for="necklace_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Warna Kalung</label>
                    <input type="text" id="necklace_color" name="necklace_color" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                 <div>
                    <label for="current_location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang</label>
                    <select id="current_location_id" name="current_location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Simpan Data Kelahiran
            </button>
        </form>
    </div>
</x-app-layout>
