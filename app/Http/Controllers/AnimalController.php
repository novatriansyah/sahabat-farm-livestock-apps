<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\MasterCategory;
use App\Models\MasterBreed;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\MasterPartner;
use App\Models\User;
use App\Models\WeightLog;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Exports\AnimalsExport;
use App\Imports\AnimalsImport;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Laravel\Facades\Image;

class AnimalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Animal::with(['category', 'breed', 'location', 'physStatus', 'photos']);

        // 1. Role Scoping
        if (auth()->user()->role === 'MITRA') {
            $query->where('partner_id', auth()->user()->partner_id);
        }

        // 2. Search Scope
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tag_id', 'like', "%{$search}%")
                    ->orWhereHas('breed', function ($bq) use ($search) {
                        $bq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 3. Expanded Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('breed_id')) {
            $query->where('breed_id', $request->breed_id);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('phys_status_id')) {
            $query->where('current_phys_status_id', $request->phys_status_id);
        }
        if ($request->filled('location_id')) {
            $query->where('current_location_id', $request->location_id);
        }
        if ($request->filled('partner_id') && auth()->user()->role === 'PEMILIK') {
            $query->where('partner_id', $request->partner_id);
        }

        $animals = $query->latest()->paginate(20);

        // Fetch filter options
        $breeds = MasterBreed::all();
        $categories = MasterCategory::all();
        $locations = MasterLocation::all();
        $statuses = MasterPhysStatus::all();
        $partners = MasterPartner::all();

        return view('animals.index', compact('animals', 'breeds', 'categories', 'locations', 'statuses', 'partners'));
    }

    public function downloadTemplate()
    {
        return Excel::download(new AnimalsExport, 'template_ternak_sfi.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB Max
        ]);

        try {
            $import = new AnimalsImport;
            Excel::import($import, $request->file('file'));

            $msg = "Import Berhasil! {$import->importedCount} data masuk. {$import->skippedCount} data duplikat dilewati.";

            return redirect()->route('animals.index')->with('success', $msg);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $row = $failure->row();
                $attrib = $failure->attribute();
                foreach ($failure->errors() as $error) {
                    $errorMessages[] = "Baris {$row} ({$attrib}): {$error}";
                }
            }

            return redirect()->route('animals.index')->with('error', $errorMessages);
        } catch (\Exception $e) {
            return redirect()->route('animals.index')->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    public function create(): View
    {
        $categories = MasterCategory::all();
        $breeds = MasterBreed::all();
        $locations = MasterLocation::all();
        $statuses = MasterPhysStatus::all();
        $partners = MasterPartner::all();

        $necklaceColors = array_filter(array_map('trim', explode(',', \App\Models\FarmSetting::get('available_necklace_colors', 'Merah,Biru,Hijau,Kuning,Hitam,Putih'))));
        $earTagColors = array_filter(array_map('trim', explode(',', \App\Models\FarmSetting::get('available_ear_tag_colors', 'Merah,Biru,Hijau,Kuning,Hitam,Orange,Orange Persegi,Hijau Persegi,Kuning Orange'))));

        return view('animals.create', compact('categories', 'breeds', 'locations', 'statuses', 'partners', 'necklaceColors', 'earTagColors'));
    }

    public function store(Request $request, TaskService $taskService): RedirectResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|unique:animals,tag_id',
            'partner_id' => 'nullable|exists:master_partners,id',
            'breed_id' => 'required|exists:master_breeds,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'current_phys_status_id' => 'required|exists:master_phys_statuses,id',
            'gender' => 'required|in:JANTAN,BETINA',
            'birth_date' => 'required|date',
            'entry_date' => 'nullable|date',
            'acquisition_type' => 'required|in:HASIL_TERNAK,BELI',
            'purchase_price' => 'nullable|required_if:acquisition_type,BELI|numeric|min:0',
            'initial_weight' => 'required|numeric|min:0.1',
            'necklace_color' => 'nullable|string',
            'ear_tag_color' => 'nullable|string',
            'health_status' => 'nullable|in:SEHAT,SAKIT,KARANTINA,MATI,TERJUAL',
            'generation' => 'nullable|string',
            'google_drive_link' => 'nullable|url',
            'photo' => 'nullable|array',
            'photo.*' => 'nullable|image|max:10240', // 10MB Max per file
            'is_for_sale' => 'boolean',
            'sale_price' => 'required_if:is_for_sale,true|nullable|numeric|min:0',
            'sale_description' => 'required_if:is_for_sale,true|nullable|string|max:1000',
        ]);

        // Auto-assign owner_id to current user (System User)
        $validated['owner_id'] = auth()->id();

        // Auto-assign category from breed
        $breed = MasterBreed::find($validated['breed_id']);
        $validated['category_id'] = $breed ? $breed->category_id : null;

        if (isset($validated['purchase_price']) && $validated['acquisition_type'] !== 'BELI') {
            $validated['purchase_price'] = 0;
        }

        $validated['is_for_sale'] = (bool)($validated['is_for_sale'] ?? false);
        if (!$validated['is_for_sale']) {
            $validated['sale_price'] = null;
            $validated['sale_description'] = null;
        }

        // Set entry_date logic
        // If BOUGHT: entry_date is assumed to be today (or could be added to form later). We default to now.
        // If BRED: entry_date is birth_date.
        // Calculate Age
        $birthDate = Carbon::parse($validated['birth_date']);
        $ageInDays = $birthDate->diffInDays(Carbon::now());

        // Auto-assign Status/Location for Kids (Cempe)
        $kidThreshold = (int) \App\Models\FarmSetting::get('kid_threshold_days', 40);
        if ($ageInDays < $kidThreshold) {
            $kidStatus = \App\Models\MasterPhysStatus::where('name', 'like', '%Cempe%')->first();
            $kidLocation = \App\Models\MasterLocation::where('name', 'like', '%Cempe%')->first();
            if ($kidStatus) $validated['current_phys_status_id'] = $kidStatus->id;
            if ($kidLocation) $validated['current_location_id'] = $kidLocation->id;
        }

        if ($validated['acquisition_type'] === 'HASIL_TERNAK') {
            $validated['entry_date'] = $validated['birth_date'];
        } else {
            $validated['entry_date'] = $validated['entry_date'] ?? Carbon::now();
        }

        // Extract initial_weight and photo before creating Animal (as they are not in animals table)
        $initialWeight = $validated['initial_weight'];
        unset($validated['initial_weight']);
        unset($validated['photo']);

        $animal = Animal::create($validated);

        // Record Initial Weight
        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => Carbon::now(),
            'weight_kg' => $initialWeight,
        ]);

        // Generate Tasks if Bought (SOP Kedatangan)
        if ($validated['acquisition_type'] === 'BELI') {
            $taskService->generateArrivalTasks($animal);
        }

        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $filename = 'animal-photos/' . uniqid() . '.webp';

                // Optimize: Resize to 800px width, convert to WebP, Quality 75%
                $image = Image::read($file);
                $image->scale(width: 800);
                $encoded = $image->toWebp(75);

                Storage::disk('public')->put($filename, (string) $encoded);

                $animal->photos()->create([
                    'photo_url' => $filename,
                    'capture_date' => Carbon::now(),
                ]);
            }
        }

        return redirect()->route('animals.index')->with('success', 'Ternak berhasil ditambahkan.');
    }

    public function show(Animal $animal): View
    {
        $this->authorize('view', $animal);

        $animal->load(['category', 'breed', 'location', 'physStatus', 'photos', 'weightLogs', 'treatmentLogs', 'owner', 'ownershipLogs.oldPartner', 'ownershipLogs.newPartner', 'earTagLogs', 'offspring', 'sire', 'dam']);

        // Prepare Chart Data
        $weightLogs = $animal->weightLogs()->orderBy('weigh_date', 'asc')->get();
        $weightLabels = $weightLogs->pluck('weigh_date')->map(fn($d) => $d->format('d M Y'));
        $weightData = $weightLogs->pluck('weight_kg');

        $diseases = \App\Models\MasterDisease::with('recommendedTreatments')->get();
        $locations = \App\Models\MasterLocation::all();
        $medicines = \App\Models\InventoryItem::whereIn('category', ['Obat-Obatan', 'Vitamin', 'Vaksin'])->get();

        return view('animals.show', compact('animal', 'weightLabels', 'weightData', 'diseases', 'locations', 'medicines'));
    }

    public function edit(Animal $animal): View
    {
        $this->authorize('update', $animal);
        $categories = MasterCategory::all();
        $breeds = MasterBreed::all();
        $locations = MasterLocation::all();
        $statuses = MasterPhysStatus::all();
        $partners = MasterPartner::all();
        $sires = Animal::where('gender', 'JANTAN')->where('is_active', true)->where('id', '!=', $animal->id)->get();
        $dams = Animal::where('gender', 'BETINA')->where('is_active', true)->where('id', '!=', $animal->id)->get();

        $necklaceColors = array_filter(array_map('trim', explode(',', \App\Models\FarmSetting::get('available_necklace_colors', 'Merah,Biru,Hijau,Kuning,Hitam,Putih'))));
        $earTagColors = array_filter(array_map('trim', explode(',', \App\Models\FarmSetting::get('available_ear_tag_colors', 'Merah,Biru,Hijau,Kuning,Hitam,Orange,Orange Persegi,Hijau Persegi,Kuning Orange'))));

        return view('animals.edit', compact('animal', 'categories', 'breeds', 'locations', 'statuses', 'partners', 'sires', 'dams', 'necklaceColors', 'earTagColors'));
    }

    public function update(Request $request, Animal $animal): RedirectResponse
    {
        $this->authorize('update', $animal);
        $validated = $request->validate([
            'tag_id' => [
                'required',
                Rule::unique('animals')->ignore($animal->id)
            ],
            'legacy_tag_id' => 'nullable|string',
            'partner_id' => 'nullable|exists:master_partners,id',
            'breed_id' => 'required|exists:master_breeds,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'current_phys_status_id' => 'required|exists:master_phys_statuses,id',
            'gender' => 'required|in:JANTAN,BETINA',
            'birth_date' => 'required|date',
            'entry_date' => 'nullable|date',
            'health_status' => 'required|in:SEHAT,SAKIT,KARANTINA,MATI,TERJUAL',
            'physical_characteristics' => 'nullable|string',
            'birth_weight' => 'nullable|numeric|min:0',
            'valuation' => 'nullable|numeric|min:0',
            'acquisition_type' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_inventory_status' => 'nullable|string',
            'is_active' => 'boolean',
            'litter_size' => 'nullable|integer|min:0',
            'total_cycles' => 'nullable|integer|min:0',
            'data_source' => 'nullable|string',
            'confidence' => 'nullable|string|in:HIGH,MEDIUM,LOW',
            'in_partner_file' => 'boolean',
            'notes' => 'nullable|string',
            'necklace_color' => 'nullable|string',
            'ear_tag_color' => 'nullable|string',
            'generation' => 'nullable|string',
            'google_drive_link' => 'nullable|url',
            'photo' => 'nullable|array',
            'photo.*' => 'nullable|image|max:10240',
            'sire_id' => [
                'nullable',
                'exists:animals,id',
                Rule::notIn([$animal->id])
            ],
            'dam_id' => [
                'nullable',
                'exists:animals,id',
                Rule::notIn([$animal->id])
            ],
            'is_for_sale' => 'boolean',
            'sale_price' => 'required_if:is_for_sale,true|nullable|numeric|min:0',
            'sale_description' => 'required_if:is_for_sale,true|nullable|string|max:1000',
            'change_reason' => 'nullable|string',
            'value_status' => 'nullable|string|in:ACTUAL,ESTIMATED,ASSUMED,UNKNOWN',
        ]);

        // Auto-assign category from breed
        $breed = MasterBreed::find($validated['breed_id']);
        $validated['category_id'] = $breed ? $breed->category_id : null;

        $validated['is_for_sale'] = (bool)($validated['is_for_sale'] ?? false);
        if (!$validated['is_for_sale']) {
            $validated['sale_price'] = null;
            $validated['sale_description'] = null;
        }

        $oldTagId = $animal->tag_id;
        $oldPartnerId = $animal->partner_id;
        $oldLocationId = $animal->current_location_id;

        $originalAttributes = $animal->getAttributes();
        $correlationId = (string) \Illuminate\Support\Str::uuid();
        $reason = $request->input('change_reason', 'User update via edit view');
        $newStatus = $request->input('value_status', 'ACTUAL');

        unset($validated['photo']);
        unset($validated['change_reason']);
        unset($validated['value_status']);

        $animal->update($validated);

        // Record Audit Logs in animal_field_changes
        $changedFields = [];
        foreach ($validated as $field => $newValue) {
            $oldValue = $originalAttributes[$field] ?? null;
            if ((string) $oldValue !== (string) $newValue) {
                $changedFields[] = $field;
                \App\Models\AnimalFieldChange::create([
                    'animal_id' => $animal->id,
                    'field_name' => $field,
                    'old_value' => is_array($oldValue) ? json_encode($oldValue) : (string)$oldValue,
                    'new_value' => is_array($newValue) ? json_encode($newValue) : (string)$newValue,
                    'old_value_status' => $animal->confidence === 'HIGH' ? 'ACTUAL' : 'ASSUMED',
                    'new_value_status' => $newStatus,
                    'source' => 'WEB_EDIT',
                    'reason' => $reason,
                    'changed_by' => auth()->id() ? (string)auth()->id() : 'SYSTEM',
                    'changed_at' => Carbon::now(),
                    'correlation_id' => $correlationId,
                ]);
            }
        }

        if ($oldTagId !== $animal->tag_id) {
            \App\Models\AnimalEarTagLog::create([
                'animal_id' => $animal->id,
                'old_tag_id' => $oldTagId,
                'new_tag_id' => $animal->tag_id,
                'changed_at' => Carbon::now(),
                'recorded_by' => auth()->id(),
            ]);
        }

        if ($oldPartnerId != $animal->partner_id) {
            $animal->ownershipLogs()->whereNull('end_date')->update([
                'end_date' => Carbon::now()
            ]);

            \App\Models\AnimalOwnershipLog::create([
                'animal_id' => $animal->id,
                'old_partner_id' => $oldPartnerId,
                'new_partner_id' => $animal->partner_id,
                'changed_at' => Carbon::now(),
                'recorded_by' => auth()->id(),
            ]);
        }

        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $filename = 'animal-photos/' . uniqid() . '.webp';

                $image = Image::read($file);
                $image->scale(width: 800);
                $encoded = $image->toWebp(75);

                Storage::disk('public')->put($filename, (string) $encoded);

                $animal->photos()->create([
                    'photo_url' => $filename,
                    'capture_date' => Carbon::now(),
                ]);
            }
        }

        // Dispatch SourceDataCorrected event for async recalculation & cache invalidation
        $event = new \App\Events\SourceDataCorrected(
            (string) $animal->id,
            $changedFields,
            $animal->entry_date ? $animal->entry_date->format('Y-m-d') : date('Y-m-d'),
            $oldPartnerId ? (int)$oldPartnerId : null,
            $animal->partner_id ? (int)$animal->partner_id : null,
            $oldLocationId ? (int)$oldLocationId : null,
            $animal->current_location_id ? (int)$animal->current_location_id : null,
            $correlationId,
            auth()->user()->name ?? 'System User'
        );
        app(\App\Services\RecalculationOrchestrator::class)->handleCorrection($event);

        return redirect()->route('animals.show', $animal->id)->with('success', 'Ternak berhasil diperbarui.');
    }

    /**
     * Delete a specific photo of an animal.
     */
    public function deletePhoto(Animal $animal, \App\Models\AnimalPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $animal);

        // Ensure the photo belongs to the animal
        if ($photo->animal_id !== $animal->id) {
            abort(403);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($photo->photo_url)) {
            Storage::disk('public')->delete($photo->photo_url);
        }

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
