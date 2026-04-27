<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Ternak</h2>
            <div class="flex gap-2">
                <a href="{{ route('animals.edit', $animal->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Ubah</a>
                <a href="{{ route('animals.print', $animal->id) }}" target="_blank" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cetak QR</a>
                @if($animal->is_active)
                    <a href="{{ route('animals.exit.create', $animal->id) }}" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">Keluar (Jual/Mati)</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-6">
                <div class="flex flex-col items-center pb-10">
                    @if($animal->photos->count() > 0)
                        <div class="flex overflow-x-auto space-x-2 pb-2 mb-3 w-full snap-x">
                            @foreach($animal->photos as $photo)
                                <img class="w-24 h-24 rounded-full shadow-lg object-cover flex-shrink-0 snap-center" src="{{ Storage::url($photo->photo_url) }}" alt="Animal Photo"/>
                            @endforeach
                        </div>
                    @else
                        <div class="w-24 h-24 mb-3 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $animal->tag_id }}</h5>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $animal->full_breed }} ({{ $animal->gender == 'JANTAN' ? 'Jantan' : 'Betina' }})</span>

                    <div class="mt-4 flex space-x-3 md:mt-6">
                        @if($animal->health_status == 'SEHAT')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Sehat</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $animal->health_status }}</span>
                        @endif
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $animal->physStatus->name }}</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $animal->location->name }}</span>
                        </div>
                        @if(Auth::user()->role === 'PEMILIK')
                        <form action="{{ route('operator.cage.move', $animal->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            <select name="location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-[10px] rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <option value="">Pindah...</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ $animal->current_location_id == $loc->id ? 'disabled' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="p-1 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Umur:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $animal->birth_date->diffForHumans(null, true) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemilik / Mitra:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $animal->partner->name ?? ($animal->owner->name ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">HPP Saat Ini:</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($animal->current_hpp, 0, ',', '.') }}</span>
                    </div>
                    @if($animal->google_drive_link)
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dokumen/GDrive:</span>
                        <a href="{{ $animal->google_drive_link }}" target="_blank" class="text-sm font-medium text-blue-600 hover:underline">Buka Link ↗</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tabs / History -->
            <div class="md:col-span-2">
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="weight-tab" data-tabs-target="#weight" type="button" role="tab" aria-controls="weight" aria-selected="false">Riwayat Bobot</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="health-tab" data-tabs-target="#health" type="button" role="tab" aria-controls="health" aria-selected="false">Riwayat Kesehatan</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="sop-tab" data-tabs-target="#sop" type="button" role="tab" aria-controls="sop" aria-selected="false">Tugas SOP</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="timeline-tab" data-tabs-target="#timeline" type="button" role="tab" aria-controls="timeline" aria-selected="false">Timeline</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="offspring-tab" data-tabs-target="#offspring" type="button" role="tab" aria-controls="offspring" aria-selected="false">Keturunan</button>
                        </li>
                        <li role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="breeding-tab" data-tabs-target="#breeding" type="button" role="tab" aria-controls="breeding" aria-selected="false">Riwayat Kawin</button>
                        </li>
                    </ul>
                </div>
                <div id="myTabContent">
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="weight" role="tabpanel" aria-labelledby="weight-tab">
                        <!-- Chart Container -->
                        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm dark:bg-gray-700">
                            @if(Auth::user()->role === 'PEMILIK')
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Input Bobot Baru</h4>
                                    <form action="{{ route('operator.weight.store', $animal->id) }}" method="POST" class="flex items-end gap-4">
                                        @csrf
                                        <div class="flex-1">
                                            <label for="weight_kg" class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Bobot Saat Ini (kg)</label>
                                            <input type="number" id="weight_kg" name="weight_kg" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                                        </div>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-500 dark:hover:bg-primary-600 focus:outline-none dark:focus:ring-primary-800">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                            @endif
                            <canvas id="individualWeightChart" height="100"></canvas>
                        </div>
                        
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tanggal</th>
                                        <th scope="col" class="px-6 py-3">Bobot (kg)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animal->weightLogs()->orderByDesc('weigh_date')->get() as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $log->weigh_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 font-bold">{{ $log->weight_kg }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="health" role="tabpanel" aria-labelledby="health-tab"
                         x-data="{ 
                            diseases: {{ $diseases->map(function($d) {
                                return [
                                    'id' => $d->id,
                                    'symptoms' => $d->symptoms,
                                    'treatments' => $d->recommendedTreatments->map(function($t) {
                                        return [
                                            'name' => $t->name,
                                            'dosage' => $t->pivot->custom_dosage ?: ($t->dosage_per_kg ? $t->dosage_per_kg . ' ' . $t->unit . '/kg' : '-')
                                        ];
                                    })
                                ];
                            })->toJson() }},
                            selectedDisease: '',
                            recommendation: { symptoms: '', treatments: [] },
                            updateRecommendation() {
                                const disease = this.diseases.find(d => d.id == this.selectedDisease);
                                if (disease) {
                                    this.recommendation = { symptoms: disease.symptoms, treatments: disease.treatments };
                                } else {
                                    this.recommendation = { symptoms: '', treatments: [] };
                                }
                            }
                         }">
                        @if(Auth::user()->role === 'PEMILIK')
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-600 bg-white p-4 rounded-lg shadow-sm dark:bg-gray-700">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Update Status Kesehatan</h4>
                            <form action="{{ route('operator.health.store', $animal->id) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="health_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                            <option value="SEHAT" {{ $animal->health_status == 'SEHAT' ? 'selected' : '' }}>SEHAT</option>
                                            <option value="SAKIT" {{ $animal->health_status == 'SAKIT' ? 'selected' : '' }}>SAKIT</option>
                                            <option value="KARANTINA" {{ $animal->health_status == 'KARANTINA' ? 'selected' : '' }}>KARANTINA</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Diagnosis Penyakit (Opsional)</label>
                                        <select name="disease_id" x-model="selectedDisease" @change="updateRecommendation()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Pilih Penyakit...</option>
                                            @foreach($diseases as $disease)
                                                <option value="{{ $disease->id }}">{{ $disease->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
 
                                <!-- Disease Recommendation Display -->
                                <template x-if="recommendation.symptoms || recommendation.treatments.length > 0">
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl dark:bg-blue-900/20 dark:border-blue-800">
                                        <template x-if="recommendation.symptoms">
                                            <div class="mb-2">
                                                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Gejala Umum</span>
                                                <p class="text-xs text-gray-700 dark:text-gray-300 italic" x-text="recommendation.symptoms"></p>
                                            </div>
                                        </template>
                                        <template x-if="recommendation.treatments.length > 0">
                                            <div>
                                                <span class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Rekomendasi Penanganan</span>
                                                <ul class="list-disc list-inside mt-1">
                                                    <template x-for="item in recommendation.treatments">
                                                        <li class="text-xs text-gray-700 dark:text-gray-300">
                                                            <span class="font-semibold" x-text="item.name"></span> 
                                                            <span class="text-gray-500">(Dosis: <span x-text="item.dosage"></span>)</span>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Pemberian Obat (Opsional)</label>
                                        <select name="medicine_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Pilih Obat...</option>
                                            @foreach($medicines as $med)
                                                <option value="{{ $med->id }}">{{ $med->name }} (Stok: {{ $med->current_stock }} {{ $med->unit }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Jumlah Obat (Dosis)</label>
                                        <input type="number" name="medicine_qty" step="0.01" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Catatan / Gejala</label>
                                    <textarea name="symptoms" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Catatan tambahan..."></textarea>
                                </div>
                                <button type="submit" class="mt-4 w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-500 dark:hover:bg-primary-600 focus:outline-none dark:focus:ring-primary-800">Simpan Status Kesehatan</button>
                            </form>
                        </div>
                        @endif
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tanggal</th>
                                        <th scope="col" class="px-6 py-3">Tipe</th>
                                        <th scope="col" class="px-6 py-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animal->treatmentLogs()->orderByDesc('treatment_date')->get() as $log)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $log->treatment_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                {{ $log->type === 'MEDICATION' ? 'Pemberian Obat' : 'Pemeriksaan' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $log->notes }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="sop" role="tabpanel" aria-labelledby="sop-tab">
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Jatuh Tempo</th>
                                        <th scope="col" class="px-6 py-3">Tugas</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animal->tasks()->orderBy('due_date', 'asc')->get() as $task)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">{{ $task->due_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 font-medium">{{ $task->title }}</td>
                                        <td class="px-6 py-4">
                                            @if($task->status === 'COMPLETED')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Selesai</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($task->status !== 'COMPLETED' && in_array(Auth::user()->role, ['PEMILIK', 'STAF', 'PETERNAK']))
                                                <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">Selesaikan</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($animal->tasks->isEmpty())
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada tugas SOP untuk hewan ini</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                        <ol class="relative border-s border-gray-200 dark:border-gray-700 ms-3">                  
                            @php
                                $tagLogs = $animal->earTagLogs->map(fn($log) => [
                                    'date' => $log->changed_at,
                                    'type' => 'TAG',
                                    'title' => 'Pergantian Ear Tag',
                                    'desc' => "Ganti dari tag <b>{$log->old_tag_id}</b> ke <b>{$log->new_tag_id}</b>"
                                ]);
                                
                                $ownershipLogs = $animal->ownershipLogs->map(fn($log) => [
                                    'date' => $log->changed_at,
                                    'type' => 'OWNER',
                                    'title' => 'Perubahan Kepemilikan',
                                    'desc' => "Berpindah dari <b>" . ($log->oldPartner->name ?? 'Internal SFI') . "</b> ke <b>" . ($log->newPartner->name ?? 'Internal SFI') . "</b>"
                                ]);
                                
                                $combinedLogs = $tagLogs->concat($ownershipLogs)->sortByDesc('date');
                            @endphp

                            @forelse($combinedLogs as $log)
                            <li class="mb-10 ms-6">            
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                                    @if($log['type'] === 'TAG')
                                        <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                        </svg>
                                    @else
                                        <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                                        </svg>
                                    @endif
                                </span>
                                <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $log['title'] }}</h3>
                                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ $log['date']->format('d M Y') }}</time>
                                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">{!! $log['desc'] !!}</p>
                            </li>
                            @empty
                            <p class="text-gray-500 italic text-sm ms-2">Belum ada riwayat timeline (tag/kepemilikan) untuk hewan ini.</p>
                            @endforelse
                        </ol>
                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="offspring" role="tabpanel" aria-labelledby="offspring-tab">
                        <!-- Ancestry (Silsilah) -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Pejantan (Ayah)</p>
                                @if($animal->sire)
                                    <a href="{{ route('animals.show', $animal->sire->id) }}" class="text-lg font-bold text-blue-600 hover:underline">{{ $animal->sire->tag_id }}</a>
                                    <p class="text-xs text-gray-500">{{ $animal->sire->full_breed }}</p>
                                @else
                                    <p class="text-gray-400 italic text-sm">Data tidak tersedia</p>
                                @endif
                            </div>
                            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Indukan (Ibu)</p>
                                @if($animal->dam)
                                    <a href="{{ route('animals.show', $animal->dam->id) }}" class="text-lg font-bold text-blue-600 hover:underline">{{ $animal->dam->tag_id }}</a>
                                    <p class="text-xs text-gray-500">{{ $animal->dam->full_breed }}</p>
                                @else
                                    <p class="text-gray-400 italic text-sm">Data tidak tersedia</p>
                                @endif
                            </div>
                        </div>

                        <div class="relative overflow-x-auto">
                            <h4 class="text-sm font-semibold mb-3 text-gray-900 dark:text-white uppercase tracking-wider">Daftar Keturunan (Anak)</h4>
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tag Keturunan</th>
                                        <th scope="col" class="px-6 py-3">Tanggal Lahir</th>
                                        <th scope="col" class="px-6 py-3">Jenis Kelamin</th>
                                        <th scope="col" class="px-6 py-3">Status Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($animal->offspring()->orderByDesc('birth_date')->get() as $child)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 font-bold"><a href="{{ route('animals.show', $child->id) }}" class="text-blue-600 hover:underline">{{ $child->tag_id }}</a></td>
                                        <td class="px-6 py-4">{{ $child->birth_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">{{ $child->gender == 'JANTAN' ? 'Jantan' : 'Betina' }}</td>
                                        <td class="px-6 py-4">{{ $child->physStatus->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @if($animal->offspring->isEmpty())
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada catatan keturunan</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="breeding" role="tabpanel" aria-labelledby="breeding-tab">
                        @php
                            $events = $animal->breedingEvents()->orderByDesc('mating_date')->get();
                            $totalEvents = $events->count();
                            $successEvents = $events->whereIn('status', ['SUCCESS', 'COMPLETED'])->count();
                            $rate = $totalEvents > 0 ? ($successEvents / $totalEvents) * 100 : 0;
                        @endphp

                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-700 border-l-4 border-blue-500">
                                <p class="text-xs text-gray-500 uppercase font-bold">Total Kawin</p>
                                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalEvents }} x</p>
                            </div>
                            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-700 border-l-4 border-green-500">
                                <p class="text-xs text-gray-500 uppercase font-bold">Berhasil</p>
                                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $successEvents }} x</p>
                            </div>
                            <div class="p-4 bg-white rounded-lg shadow-sm dark:bg-gray-700 border-l-4 border-purple-500">
                                <p class="text-xs text-gray-500 uppercase font-bold">Success Rate</p>
                                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($rate, 1) }}%</p>
                            </div>
                        </div>

                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tanggal Kawin</th>
                                        <th scope="col" class="px-6 py-3">Pasangan</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Est. Kelahiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4 font-medium">{{ $event->mating_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $partner = $animal->gender === 'JANTAN' ? $event->dam : $event->sire;
                                            @endphp
                                            @if($partner)
                                                <a href="{{ route('animals.show', $partner->id) }}" class="text-blue-600 hover:underline">{{ $partner->tag_id }}</a>
                                                <span class="text-xs text-gray-400">({{ $partner->breed->name ?? '-' }})</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($event->status === 'SUCCESS' || $event->status === 'COMPLETED')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Berhasil</span>
                                            @elseif($event->status === 'FAILED')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Gagal</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Menunggu</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs">{{ $event->est_birth_date ? $event->est_birth_date->format('d M Y') : '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @if($events->isEmpty())
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada riwayat perkawinan</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('individualWeightChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($weightLabels),
                        datasets: [{
                            label: 'Bobot (kg)',
                            data: @json($weightData),
                            borderColor: '#1A56DB', // Blue 600
                            backgroundColor: 'rgba(26, 86, 219, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#1A56DB',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: false, // Weight usually doesn't drop to 0
                                title: { display: true, text: 'Kg' }
                            },
                             x: {
                                title: { display: true, text: 'Tanggal Timbang' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
