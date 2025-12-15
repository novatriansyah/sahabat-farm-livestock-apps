<x-app-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Master Data Management</h2>
        <p class="text-gray-500 dark:text-gray-400">Manage farm configurations (Breeds, Locations, Diseases).</p>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Breeds Section -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Breeds</h3>

            <form action="{{ route('masters.breed.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Breed Name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <select name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="1">Domba</option>
                        <option value="2">Kambing</option>
                    </select>
                    <input type="number" name="min_weight_mate" placeholder="Min Weight (kg)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <input type="number" name="min_age_mate_months" placeholder="Min Age (Months)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Add Breed</button>
            </form>

            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($breeds as $breed)
                    <li>{{ $breed->name }} ({{ $breed->category->name ?? '-' }})</li>
                @endforeach
            </ul>
        </div>

        <!-- Locations Section -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Locations</h3>

            <form action="{{ route('masters.location.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="grid gap-4 mb-4 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Location Name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    <select name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="Kandang Individu">Kandang Individu</option>
                        <option value="Kandang Koloni">Kandang Koloni</option>
                        <option value="Quarantine">Quarantine</option>
                    </select>
                </div>
                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700">Add Location</button>
            </form>

            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 h-48 overflow-y-auto">
                @foreach($locations as $loc)
                    <li>{{ $loc->name }} ({{ $loc->type }})</li>
                @endforeach
            </ul>
        </div>
    </div>
</x-app-layout>
