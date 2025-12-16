<x-app-layout>
    <div class="max-w-md mx-auto bg-white p-4 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-xl font-bold mb-4 dark:text-white">Pemberian Pakan Harian & Stok</h2>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inventory.usage.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="item_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Pakan/Barang</label>
                <select name="item_id" id="item_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->current_stock }} {{ $item->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang (Opsional)</label>
                <select name="location_id" id="location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">-- Umum / Seluruh Farm --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih kandang untuk perhitungan HPP yang presisi.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="qty_used" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Dipakai</label>
                    <input type="number" name="qty_used" id="qty_used" step="0.1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="qty_wasted" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Terbuang</label>
                    <input type="number" name="qty_wasted" id="qty_wasted" step="0.1" value="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="usage_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal</label>
                <input type="date" name="usage_date" id="usage_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
            </div>

            <button type="submit" class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">Catat Penggunaan</button>
        </form>
    </div>
</x-app-layout>
