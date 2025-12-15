<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Edit Location: {{ $location->name }}</h2>
        <form action="{{ route('masters.location.update', $location->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $location->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                    <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="Kandang Individu" {{ $location->type == 'Kandang Individu' ? 'selected' : '' }}>Kandang Individu</option>
                        <option value="Kandang Koloni" {{ $location->type == 'Kandang Koloni' ? 'selected' : '' }}>Kandang Koloni</option>
                        <option value="Quarantine" {{ $location->type == 'Quarantine' ? 'selected' : '' }}>Quarantine</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700">Update Location</button>
        </form>
    </div>
</x-app-layout>
