<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Ubah Breed: {{ $breed->name }}</h2>
        <form action="{{ route('masters.breed.update', $breed->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-8 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Bibit</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $breed->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="category_id" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Jenis Hewan</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $breed->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="min_weight_mate" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Berat Min. Kawin (kg)</label>
                    <input type="number" id="min_weight_mate" name="min_weight_mate" min="0" value="{{ old('min_weight_mate', $breed->min_weight_mate) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="min_age_mate_months" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Umur Min. Kawin (Bulan)</label>
                    <input type="number" id="min_age_mate_months" name="min_age_mate_months" min="0" value="{{ old('min_age_mate_months', $breed->min_age_mate_months) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="md:col-span-2">
                    <label for="origin" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Asal / Origin</label>
                    <input type="text" id="origin" name="origin" value="{{ old('origin', $breed->origin) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('masters.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Batal</a>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
