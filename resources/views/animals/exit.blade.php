<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Register Exit: {{ $animal->tag_id }}</h2>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Current HPP (Accumulated Cost)</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($animal->current_hpp, 0, ',', '.') }}</p>
        </div>

        <form action="{{ route('animals.exit.store', $animal->id) }}" method="POST">
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-1">
                <div>
                    <label for="exit_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Exit Type</label>
                    <select id="exit_type" name="exit_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" onchange="togglePriceField(this.value)">
                        <option value="SALE">Sale</option>
                        <option value="SLAUGHTER">Potong (Slaughter)</option>
                        <option value="DEATH">Death</option>
                    </select>
                </div>
                <div>
                    <label for="exit_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                    <input type="date" id="exit_date" name="exit_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div id="price-field">
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sale Price (IDR)</label>
                    <input type="number" id="price" name="price" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div>
                    <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes / Cause of Death</label>
                    <textarea id="notes" name="notes" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                </div>
            </div>
            <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Process Exit</button>
        </form>
    </div>

    <script>
        function togglePriceField(type) {
            const field = document.getElementById('price-field');
            if (type === 'DEATH') {
                field.style.display = 'none';
                document.getElementById('price').value = 0;
            } else {
                field.style.display = 'block';
            }
        }
    </script>
</x-app-layout>
