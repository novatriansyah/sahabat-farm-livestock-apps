<x-app-layout>
    <div class="max-w-md mx-auto bg-white p-4 rounded-lg shadow dark:bg-gray-800">

        <!-- Header -->
        <div class="flex items-center space-x-4 mb-6">
            <div class="flex-shrink-0">
                @if($animal->photos->count() > 0)
                    <img class="w-16 h-16 rounded-full object-cover" src="{{ Storage::url($animal->photos->first()->photo_url) }}" alt="Animal">
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                    {{ $animal->tag_id }}
                </p>
                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                    {{ $animal->breed->name }} - {{ $animal->location->name }}
                </p>
                <div class="mt-1">
                     @if($animal->health_status == 'HEALTHY')
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Healthy</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $animal->health_status }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Success Message -->
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

        <!-- Tabs -->
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="weight-tab" data-tabs-target="#weight" type="button" role="tab" aria-controls="weight" aria-selected="false">Timbang</button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="health-tab" data-tabs-target="#health" type="button" role="tab" aria-controls="health" aria-selected="false">Kesehatan</button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="move-tab" data-tabs-target="#move" type="button" role="tab" aria-controls="move" aria-selected="false">Pindah Kandang</button>
                </li>
            </ul>
        </div>

        <div id="default-tab-content">
            <!-- Weight Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-900" id="weight" role="tabpanel" aria-labelledby="weight-tab">
                <form action="{{ route('operator.weight.store', $animal->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="weight_kg" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Berat Saat Ini (kg)</label>
                        <input type="number" id="weight_kg" name="weight_kg" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Berat</button>
                </form>
            </div>

            <!-- Health Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-900" id="health" role="tabpanel" aria-labelledby="health-tab">
                <form action="{{ route('operator.health.store', $animal->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="health_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="health_status" name="health_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="HEALTHY" {{ $animal->health_status == 'HEALTHY' ? 'selected' : '' }}>Sehat (Healthy)</option>
                            <option value="SICK" {{ $animal->health_status == 'SICK' ? 'selected' : '' }}>Sakit (Sick)</option>
                            <option value="QUARANTINE" {{ $animal->health_status == 'QUARANTINE' ? 'selected' : '' }}>Karantina</option>
                        </select>
                    </div>

                    <!-- Disease Selection -->
                    <div class="mb-4">
                        <label for="disease_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diagnosa (Opsional)</label>
                        <select id="disease_id" name="disease_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($diseases as $disease)
                                <option value="{{ $disease->id }}">{{ $disease->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="symptoms" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gejala / Catatan</label>
                        <textarea id="symptoms" name="symptoms" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
                    </div>

                    <hr class="my-4 border-gray-200 dark:border-gray-700">
                    <p class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Obat yang Digunakan (Opsional)</p>

                    <div class="mb-4">
                        <label for="medicine_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Obat</label>
                        <select id="medicine_id" name="medicine_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" onchange="calculateDosage(this)">
                            <option value="">-- Pilih Obat --</option>
                            @foreach($medicines as $med)
                                <option value="{{ $med->id }}" data-dosage="{{ $med->dosage_per_kg }}">{{ $med->name }} ({{ $med->current_stock }} {{ $med->unit }})</option>
                            @endforeach
                        </select>
                        <p id="dosage-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                    </div>
                    <div class="mb-4">
                        <label for="medicine_qty" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah (Dosis)</label>
                        <input type="number" id="medicine_qty" name="medicine_qty" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    <button type="submit" class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">Update Kesehatan</button>
                </form>
            </div>

            <!-- Move Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-900" id="move" role="tabpanel" aria-labelledby="move-tab">
                 <form action="{{ route('operator.cage.move', $animal->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="location_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pindah ke Kandang</label>
                        <select id="location_id" name="location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $animal->current_location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Pindah Kandang</button>
                 </form>
            </div>
        </div>

    </div>

    <script>
        function calculateDosage(select) {
            const dosagePerKg = select.options[select.selectedIndex].getAttribute('data-dosage');
            // Assuming current weight is known or passed to view.
            // For MVP, we'll just show the hint rule.
            const hint = document.getElementById('dosage-hint');
            if (dosagePerKg) {
                hint.textContent = `Recommended Dosage: ${dosagePerKg} per kg`;

                // If we want to auto-fill, we need the animal's weight.
                // We can pass it via PHP to a JS variable.
                const currentWeight = {{ $animal->weightLogs()->orderByDesc('weigh_date')->first()->weight_kg ?? 0 }};
                if (currentWeight > 0) {
                    const recommended = (currentWeight * dosagePerKg).toFixed(2);
                    document.getElementById('medicine_qty').value = recommended;
                    hint.textContent += ` (Auto-calc for ${currentWeight}kg: ${recommended})`;
                }
            } else {
                hint.textContent = '';
            }
        }
    </script>
</x-app-layout>
