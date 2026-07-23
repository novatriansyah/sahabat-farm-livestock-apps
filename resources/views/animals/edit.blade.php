<x-app-layout>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow dark:bg-gray-800" x-data="{ activeTab: 'identity' }">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit & Koreksi Ternak: {{ $animal->tag_id }}</h2>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                Data Status: {{ $animal->confidence === 'HIGH' ? 'AKTUAL' : 'ASUMSI (PROVISIONAL)' }}
            </span>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                <li class="mr-2">
                    <button type="button" @click="activeTab = 'identity'" :class="activeTab === 'identity' ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 border-b-2'" class="inline-block p-4 border-b-2 rounded-t-lg">1. Identitas & Kepemilikan</button>
                </li>
                <li class="mr-2">
                    <button type="button" @click="activeTab = 'biological'" :class="activeTab === 'biological' ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 border-b-2'" class="inline-block p-4 border-b-2 rounded-t-lg">2. Biologis & Lineage</button>
                </li>
                <li class="mr-2">
                    <button type="button" @click="activeTab = 'status_location'" :class="activeTab === 'status_location' ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 border-b-2'" class="inline-block p-4 border-b-2 rounded-t-lg">3. Status & Lokasi</button>
                </li>
                <li class="mr-2">
                    <button type="button" @click="activeTab = 'valuation'" :class="activeTab === 'valuation' ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 border-b-2'" class="inline-block p-4 border-b-2 rounded-t-lg">4. Perolehan & Valuasi</button>
                </li>
                <li class="mr-2">
                    <button type="button" @click="activeTab = 'audit'" :class="activeTab === 'audit' ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 border-b-2'" class="inline-block p-4 border-b-2 rounded-t-lg">5. Histori Koreksi</button>
                </li>
            </ul>
        </div>

        <form action="{{ route('animals.update', $animal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tab 1: Identitas & Kepemilikan -->
            <div x-show="activeTab === 'identity'" class="space-y-4">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="tag_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag ID (Ear Tag)</label>
                        <input type="text" id="tag_id" name="tag_id" value="{{ old('tag_id', $animal->tag_id) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label for="legacy_tag_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Legacy Tag ID (Kode Lama)</label>
                        <input type="text" id="legacy_tag_id" name="legacy_tag_id" value="{{ old('legacy_tag_id', $animal->legacy_tag_id) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="partner_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mitra (Kepemilikan)</label>
                        <select id="partner_id" name="partner_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">-- SFI (Internal) --</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ $animal->partner_id == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="in_partner_file" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Dalam File Partner</label>
                        <select id="in_partner_file" name="in_partner_file" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="1" {{ $animal->in_partner_file ? 'selected' : '' }}>Tercantum di File Partner</option>
                            <option value="0" {{ !$animal->in_partner_file ? 'selected' : '' }}>Tidak Tercantum (Master Only)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Biologis & Lineage -->
            <div x-show="activeTab === 'biological'" class="space-y-4">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="breed_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ras / Breed</label>
                        <select id="breed_id" name="breed_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach($breeds as $breed)
                                <option value="{{ $breed->id }}" {{ $animal->breed_id == $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="generation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Generasi</label>
                        <select id="generation" name="generation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach(['F1', 'F2', 'F3', 'F4', 'F5', 'PURE', 'CROSS'] as $gen)
                                <option value="{{ $gen }}" {{ $animal->generation == $gen ? 'selected' : '' }}>{{ $gen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="gender" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="JANTAN" {{ $animal->gender == 'JANTAN' ? 'selected' : '' }}>Jantan</option>
                            <option value="BETINA" {{ $animal->gender == 'BETINA' ? 'selected' : '' }}>Betina</option>
                        </select>
                    </div>
                    <div>
                        <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $animal->birth_date ? $animal->birth_date->format('Y-m-d') : '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label for="sire_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sire (Ayah)</label>
                        <select id="sire_id" name="sire_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">-- Tidak Diketahui --</option>
                            @foreach($sires as $sire)
                                <option value="{{ $sire->id }}" {{ $animal->sire_id == $sire->id ? 'selected' : '' }}>{{ $sire->tag_id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="dam_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dam (Ibu)</label>
                        <select id="dam_id" name="dam_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">-- Tidak Diketahui --</option>
                            @foreach($dams as $dam)
                                <option value="{{ $dam->id }}" {{ $animal->dam_id == $dam->id ? 'selected' : '' }}>{{ $dam->tag_id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="birth_weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bobot Lahir (kg)</label>
                        <input type="number" step="0.1" id="birth_weight" name="birth_weight" value="{{ old('birth_weight', $animal->birth_weight) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="litter_size" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Anakan per Kelahiran (Litter Size)</label>
                        <input type="number" id="litter_size" name="litter_size" value="{{ old('litter_size', $animal->litter_size) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Tab 3: Status & Lokasi -->
            <div x-show="activeTab === 'status_location'" class="space-y-4">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="current_location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lokasi Kandang</label>
                        <select id="current_location_id" name="current_location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ $animal->current_location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="current_phys_status_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Fisik</label>
                        <select id="current_phys_status_id" name="current_phys_status_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $animal->current_phys_status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="health_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Kesehatan</label>
                        <select id="health_status" name="health_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach(['SEHAT' => 'Sehat', 'SAKIT' => 'Sakit', 'KARANTINA' => 'Karantina', 'MATI' => 'Mati', 'TERJUAL' => 'Terjual'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $animal->health_status == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Keaktifan Populer</label>
                        <select id="is_active" name="is_active" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="1" {{ $animal->is_active ? 'selected' : '' }}>Aktif di Kandang</option>
                            <option value="0" {{ !$animal->is_active ? 'selected' : '' }}>Non-Aktif (Keluar / Mati / Terjual)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Perolehan & Valuasi -->
            <div x-show="activeTab === 'valuation'" class="space-y-4">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="entry_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Masuk Kandang</label>
                        <input type="date" id="entry_date" name="entry_date" value="{{ old('entry_date', $animal->entry_date ? $animal->entry_date->format('Y-m-d') : '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="acquisition_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Perolehan</label>
                        <select id="acquisition_type" name="acquisition_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="HASIL_TERNAK" {{ $animal->acquisition_type == 'HASIL_TERNAK' ? 'selected' : '' }}>Hasil Ternak Internal</option>
                            <option value="BELI" {{ $animal->acquisition_type == 'BELI' ? 'selected' : '' }}>Beli Dari Luar</option>
                        </select>
                    </div>
                    <div>
                        <label for="purchase_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga Pembelian Initial (Rp)</label>
                        <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $animal->purchase_price) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="valuation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Valuasi Ternak (Rp)</label>
                        <input type="number" id="valuation" name="valuation" value="{{ old('valuation', $animal->valuation) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="mt-6 p-4 border rounded-lg bg-blue-50/50 dark:bg-blue-900/20">
                    <h4 class="font-bold text-sm mb-3 text-gray-900 dark:text-white">Status Nilai Baru & Alasan Koreksi</h4>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="value_status" class="block mb-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Status Nilai Baru</label>
                            <select id="value_status" name="value_status" class="bg-white border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="ACTUAL">ACTUAL (Terkonfirmasi)</option>
                                <option value="ESTIMATED">ESTIMATED (Terukur)</option>
                                <option value="ASSUMED">ASSUMED (Asumsi Operasional)</option>
                            </select>
                        </div>
                        <div>
                            <label for="change_reason" class="block mb-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Alasan Koreksi Data</label>
                            <input type="text" id="change_reason" name="change_reason" placeholder="Contoh: Koreksi tanggal lahir dari catatan fisik kandang" class="bg-white border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Histori Koreksi -->
            <div x-show="activeTab === 'audit'" class="space-y-4">
                <h4 class="font-bold text-sm mb-3 text-gray-900 dark:text-white">Audit Trail Perubahan Field (animal_field_changes)</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-2">Waktu</th>
                                <th class="px-4 py-2">Field</th>
                                <th class="px-4 py-2">Nilai Lama</th>
                                <th class="px-4 py-2">Nilai Baru</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $changes = \App\Models\AnimalFieldChange::where('animal_id', $animal->id)->orderBy('changed_at', 'desc')->take(10)->get();
                            @endphp
                            @forelse($changes as $change)
                                <tr class="border-b dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <td class="px-4 py-2 text-xs">{{ $change->changed_at ? $change->changed_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $change->field_name }}</td>
                                    <td class="px-4 py-2 text-xs text-red-600 line-through">{{ $change->old_value ?? 'NULL' }}</td>
                                    <td class="px-4 py-2 text-xs text-emerald-600 font-semibold">{{ $change->new_value ?? 'NULL' }}</td>
                                    <td class="px-4 py-2 text-xs"><span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ $change->new_value_status }}</span></td>
                                    <td class="px-4 py-2 text-xs">{{ $change->reason }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-xs text-gray-500">Belum ada riwayat koreksi data field.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('animals.show', $animal->id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-200">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-300">Simpan Perubahan & Trigger Rekalkulasi</button>
            </div>
        </form>
    </div>
</x-app-layout>
