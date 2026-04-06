<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Ubah Location: {{ $location->name }}</h2>
        <form action="{{ route('masters.location.update', $location->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-8 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Kandang</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $location->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label for="type" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tipe</label>
                    <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="Kandang Individu" {{ $location->type == 'Kandang Individu' ? 'selected' : '' }}>Kandang Individu</option>
                        <option value="Kandang Koloni" {{ $location->type == 'Kandang Koloni' ? 'selected' : '' }}>Kandang Koloni</option>
                        <option value="Karantina" {{ $location->type == 'Karantina' ? 'selected' : '' }}>Karantina</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('masters.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Batal</a>
                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-xl text-sm px-5 py-2.5 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
