<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Ubah Disease: {{ $disease->name }}</h2>
        <form action="{{ route('masters.disease.update', $disease->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-8 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Penyakit</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $disease->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="category" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Kategori</label>
                    <select id="category" name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Viral" {{ old('category', $disease->category) == 'Viral' ? 'selected' : '' }}>Viral</option>
                        <option value="Bakteri" {{ old('category', $disease->category) == 'Bakteri' ? 'selected' : '' }}>Bakteri</option>
                        <option value="Parasit" {{ old('category', $disease->category) == 'Parasit' ? 'selected' : '' }}>Parasit</option>
                        <option value="Pakan" {{ old('category', $disease->category) == 'Pakan' ? 'selected' : '' }}>Pakan</option>
                        <option value="Lainnya" {{ old('category', $disease->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="symptoms" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Gejala</label>
                    <textarea id="symptoms" name="symptoms" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('symptoms', $disease->symptoms) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Deskripsi/Penyebab</label>
                    <textarea id="description" name="description" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $disease->description) }}</textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('masters.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Batal</a>
                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-xl text-sm px-5 py-2.5 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
