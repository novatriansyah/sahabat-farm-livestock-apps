<x-app-layout>
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800" 
         x-data="{ 
            dams: {{ $dams->map(fn($d) => ['id' => $d->id, 'partner_id' => $d->partner_id])->toJson() }},
            selectedDamId: '',
            selectedPartnerId: '',
            isPartnerLocked: false,
            updatePartner() {
                const dam = this.dams.find(d => d.id == this.selectedDamId);
                if (dam && dam.partner_id) {
                    this.selectedPartnerId = dam.partner_id;
                    this.isPartnerLocked = true;
                } else {
                    this.isPartnerLocked = false;
                }
            }
         }">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Registrasi Kelahiran (Recording Birth)</h2>
        <form action="{{ route('birth.store') }}" method="POST">
            @csrf

            <!-- Parent Info -->
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Indukan (Parents)</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="dam_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Induk Betina (Dam)</label>
                        <select id="dam_id" name="dam_id" x-model="selectedDamId" @change="updatePartner()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">-- Pilih Induk --</option>
                            @foreach($dams as $dam)
                                <option value="{{ $dam->id }}">{{ $dam->tag_id }} ({{ $dam->breed->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sire_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pejantan (Sire)</label>
                        <select id="sire_id" name="sire_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Pilih Pejantan (Opsional) --</option>
                            @foreach($sires as $sire)
                                <option value="{{ $sire->id }}">{{ $sire->tag_id }} ({{ $sire->breed->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Partner/Ownership -->
            <div class="mb-6 border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Kepemilikan (Ownership)</h3>
                <div>
                    <label for="partner_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mitra / Pemilik</label>
                    <div class="relative">
                        <select id="partner_id" name="partner_id" x-model="selectedPartnerId" :disabled="isPartnerLocked" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white disabled:bg-gray-200 dark:disabled:bg-gray-600">
                            <option value="">Tidak Diketahui</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                            @endforeach
                        </select>
                        <template x-if="isPartnerLocked">
                            <input type="hidden" name="partner_id" :value="selectedPartnerId">
                        </template>
                    </div>
                    <p class="mt-1 text-xs text-gray-500" x-show="isPartnerLocked">Otomatis mengikuti Mitra dari Induk Betina (Dam).</p>
                    <p class="mt-1 text-xs text-gray-500" x-show="!isPartnerLocked">Pilih mitra jika indukan tidak terlacak atau biarkan kosong (Tidak Diketahui).</p>
                </div>
            </div>

            <!-- Offspring Info -->
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Anak (Cempe)</h3>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="tag_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag ID (No. Telinga)</label>
                    <input type="text" id="tag_id" name="tag_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <div>
                    <label for="gender" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="JANTAN">Jantan</option>
                        <option value="BETINA">Betina</option>
                    </select>
                </div>
                <div>
                    <label for="initial_weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Berat Lahir (kg)</label>
                    <input type="number" id="initial_weight" name="initial_weight" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                 <div>
                    <label for="breed_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori & Ras (Breed)</label>
                    <select id="breed_id" name="breed_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Pilih Kategori & Ras (Opsional jika ada Pejantan) --</option>
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}">[{{ $breed->category->name }}] {{ $breed->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Jika Pejantan (Sire) dipilih, Kategori & Ras akan otomatis mengikuti Pejantan.</p>
                </div>
                <div>
                    <label for="generation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Generasi</label>
                    <select id="generation" name="generation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">-- Non-Genetik (Atau Auto-Kalkulasi jika ada Pejantan) --</option>
                        <option value="F1">F1</option>
                        <option value="F2">F2</option>
                        <option value="F3">F3</option>
                        <option value="F4">F4</option>
                        <option value="F5">F5</option>
                        <option value="F6">F6</option>
                        <option value="PURE">Purebred</option>
                        <option value="CROSS">Crossbred</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Jika Pejantan (Sire) dipilih, Generasi akan dihitung otomatis (F1+F1=F2).</p>
                </div>
                <div>
                    <label for="necklace_color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Warna Kalung</label>
                    <input type="text" id="necklace_color" name="necklace_color" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                 <div>
                    <label for="current_location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang</label>
                    <select id="current_location_id" name="current_location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Simpan Data Kelahiran
            </button>
        </form>
    </div>
</x-app-layout>
