<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Catat Perkawinan untuk {{ $animal->tag_id }}</h2>

        <!-- Eligibility Alert -->
        @if(!$eligibility['eligible'])
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">⚠️ Peringatan Validasi!</span> {{ $eligibility['reason'] }}
                <br>
                <span class="text-xs">Melanggar SOP: Tidak disarankan untuk melanjutkan.</span>
            </div>
        @else
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">✅ Siap Kawin.</span> Usia dan Bobot memenuhi standar ras.
            </div>
        @endif

        <form action="{{ route('breeding.store', $animal->id) }}" method="POST">
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-1">
                <!-- Dam Info (Read Only) -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Induk Betina (Dam)</label>
                    <input type="text" value="{{ $animal->tag_id }} - {{ $animal->breed->name }}" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" disabled>
                </div>

                <!-- Sire Selection -->
                <div>
                    <label for="sire_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Pejantan (Sire)</label>
                    <select id="sire_id" name="sire_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="">-- Pilih Pejantan --</option>
                        @foreach($sires as $sire)
                            <option value="{{ $sire->id }}">{{ $sire->tag_id }} - {{ $sire->breed->name }} ({{ $sire->breed->name == $animal->breed->name ? 'Ras Sama' : 'Silang' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label for="mating_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Kawin</label>
                    <input type="date" id="mating_date" name="mating_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
            </div>

            @if(!$eligibility['eligible'])
                <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800" onclick="return confirm('Hewan ini TIDAK memenuhi syarat SOP. Yakin ingin memaksa pencatatan?')">Paksa Catat (Force)</button>
            @else
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Data</button>
            @endif
        </form>
    </div>
</x-app-layout>
