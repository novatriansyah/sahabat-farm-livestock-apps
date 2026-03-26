<x-app-layout>
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Buat Koloni Kawin Baru</h2>
        <a href="{{ route('mating-colonies.index') }}" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">← Kembali</a>
    </div>


    <div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800">
        <form action="{{ route('mating-colonies.store') }}" method="POST">
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Koloni (Contoh: Koloni A / Periode 1)</label>
                    <input type="text" id="name" name="name" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div>
                    <label for="sire_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pejantan (Jantan)</label>
                    <select id="sire_id" name="sire_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Pilih Pejantan --</option>
                        @foreach($sires as $sire)
                            <option value="{{ $sire->id }}">{{ $sire->tag_id }} ({{ $sire->breed->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang</label>
                    <select id="location_id" name="location_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Mulai Kawin</label>
                    <input type="date" id="start_date" name="start_date" required value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Indukan (Betina)</label>
                <p class="text-sm text-gray-500 mb-3">Indukan yang dipilih akan bergabung dalam koloni dan statusnya berubah menjadi "Koloni Kawin".</p>
                
                <div class="h-64 overflow-y-auto border border-gray-200 rounded-lg p-3 dark:border-gray-700">
                    <ul class="space-y-2">
                        @forelse($dams as $dam)
                        <li>
                            <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <input id="dam_{{ $dam->id }}" type="checkbox" name="dam_ids[]" value="{{ $dam->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                <label for="dam_{{ $dam->id }}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">
                                    {{ $dam->tag_id }} ({{ $dam->breed->name }}) - Umur: {{ $dam->age_string }}
                                </label>
                            </div>
                        </li>
                        @empty
                        <li class="text-gray-500 text-sm italic p-2">Tidak ada betina yang tersedia atau bebas dari koloni lain.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <input id="force_inbreeding" type="checkbox" name="force_inbreeding" value="1" {{ old('force_inbreeding') ? 'checked' : '' }} class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                <label for="force_inbreeding" class="ms-2 text-sm font-medium text-red-600 dark:text-red-500">
                    Paksa Simpan (Abaikan Peringatan Inbreeding jika ada)
                </label>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Koloni</button>
        </form>
    </div>
</x-app-layout>
