<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Edit Data Mitra</h2>
        <form action="{{ route('partners.update', $partner) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-6 md:grid-cols-1">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Mitra</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $partner->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact_info" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Info Kontak (HP/Alamat)</label>
                    <textarea id="contact_info" name="contact_info" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('contact_info', $partner->contact_info) }}</textarea>
                    @error('contact_info')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Perubahan</button>
        </form>
    </div>
</x-app-layout>
