<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Ubah Category: {{ $category->name }}</h2>
        <form action="{{ route('masters.category.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-8">
                <div>
                    <label for="name" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Jenis Hewan</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-purple-500 focus:border-purple-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('masters.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 transition dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">Batal</a>
                <button type="submit" class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-xl text-sm px-5 py-2.5 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
