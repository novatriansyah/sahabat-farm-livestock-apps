<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Edit Item: {{ $item->name }}</h2>
        <form action="{{ route('inventory.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Item Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="unit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $item->unit) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                    <select id="category" name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="MEDICINE" {{ $item->category == 'MEDICINE' ? 'selected' : '' }}>Medicine</option>
                        <option value="VITAMIN" {{ $item->category == 'VITAMIN' ? 'selected' : '' }}>Vitamin</option>
                        <option value="VACCINE" {{ $item->category == 'VACCINE' ? 'selected' : '' }}>Vaccine</option>
                        <option value="FEED" {{ $item->category == 'FEED' ? 'selected' : '' }}>Feed</option>
                    </select>
                </div>
                <div>
                    <label for="dosage_per_kg" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dosage (per kg)</label>
                    <input type="number" id="dosage_per_kg" name="dosage_per_kg" value="{{ old('dosage_per_kg', $item->dosage_per_kg) }}" step="0.001" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update Item</button>
        </form>
    </div>
</x-app-layout>
