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
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Exports\AnimalsExport;
use App\Imports\AnimalsImport;
use Maatwebsite\Excel\Facades\Excel;

class AnimalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Animal::with(['category', 'breed', 'location', 'physStatus', 'photos']);

        // Search Scope
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tag_id', 'like', "%{$search}%")
                  ->orWhereHas('breed', function($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (auth()->user()->role === 'PARTNER') {
            $query->where('partner_id', auth()->user()->partner_id);
        }

        $animals = $query->paginate(10);
        return view('animals.index', compact('animals'));
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

        return view('animals.create', compact('categories', 'breeds', 'locations', 'statuses', 'partners'));
    }

    public function store(Request $request, TaskService $taskService): RedirectResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|unique:animals',
            'partner_id' => 'nullable|exists:master_partners,id',
            'category_id' => 'required|exists:master_categories,id',
            'breed_id' => 'required|exists:master_breeds,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'current_phys_status_id' => 'required|exists:master_phys_statuses,id',
            'gender' => 'required|in:MALE,FEMALE',
            'birth_date' => 'required|date',
            'acquisition_type' => 'required|in:BRED,BOUGHT',
            'purchase_price' => 'nullable|required_if:acquisition_type,BOUGHT|numeric|min:0',
            'initial_weight' => 'required|numeric|min:0.1',
            'necklace_color' => 'nullable|string',
            'generation' => 'nullable|string',
            'photo' => 'nullable|image|max:20480', // 20MB Max
        ]);

        // Auto-assign owner_id to current user (System User)
        $validated['owner_id'] = auth()->id();

        if (isset($validated['purchase_price']) && $validated['acquisition_type'] !== 'BOUGHT') {
            $validated['purchase_price'] = 0;
        }

        // Set entry_date logic
        // If BOUGHT: entry_date is assumed to be today (or could be added to form later). We default to now.
        // If BRED: entry_date is birth_date.
        // Calculate Age
        $birthDate = Carbon::parse($validated['birth_date']);
        $ageInDays = $birthDate->diffInDays(Carbon::now());

        // Auto-assign Status/Location for Kids (< 40 days)
        if ($ageInDays < 40) {
            $validated['current_phys_status_id'] = 1; // Cempe Lahir
            $validated['current_location_id'] = 3;    // Kandang Cempe
        }

        $validated['entry_date'] = ($validated['acquisition_type'] === 'BRED')
            ? $validated['birth_date']
            : Carbon::now();

        // Extract initial_weight before creating Animal (as it's not in animals table)
        $initialWeight = $validated['initial_weight'];
        unset($validated['initial_weight']);

        $animal = Animal::create($validated);

        // Record Initial Weight
        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => Carbon::now(),
            'weight_kg' => $initialWeight,
        ]);

        // Generate Tasks if Bought (SOP Kedatangan)
        if ($validated['acquisition_type'] === 'BOUGHT') {
            $taskService->generateArrivalTasks($animal);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('animal-photos', 'public');
            $animal->photos()->create([
                'photo_url' => $path,
                'capture_date' => Carbon::now(),
            ]);
        }

        return redirect()->route('animals.index')->with('success', 'Animal created successfully.');
    }

    public function show(Animal $animal): View
    {
        $animal->load(['category', 'breed', 'location', 'physStatus', 'photos', 'weightLogs', 'treatmentLogs', 'owner']);

        // Prepare Chart Data
        $weightLogs = $animal->weightLogs()->orderBy('weigh_date', 'asc')->get();
        $weightLabels = $weightLogs->pluck('weigh_date')->map(fn($d) => $d->format('d M Y'));
        $weightData = $weightLogs->pluck('weight_kg');

        return view('animals.show', compact('animal', 'weightLabels', 'weightData'));
    }

    public function edit(Animal $animal): View
    {
        $categories = MasterCategory::all();
        $breeds = MasterBreed::all();
        $locations = MasterLocation::all();
        $statuses = MasterPhysStatus::all();
        $partners = MasterPartner::all();

        return view('animals.edit', compact('animal', 'categories', 'breeds', 'locations', 'statuses', 'partners'));
    }

    public function update(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|unique:animals,tag_id,' . $animal->id,
            'partner_id' => 'nullable|exists:master_partners,id',
            'category_id' => 'required|exists:master_categories,id',
            'breed_id' => 'required|exists:master_breeds,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'current_phys_status_id' => 'required|exists:master_phys_statuses,id',
            'gender' => 'required|in:MALE,FEMALE',
            'birth_date' => 'required|date',
            'necklace_color' => 'nullable|string',
            'generation' => 'nullable|string',
            'photo' => 'nullable|image|max:20480',
        ]);

        $animal->update($validated);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('animal-photos', 'public');
            $animal->photos()->create([
                'photo_url' => $path,
                'capture_date' => Carbon::now(),
            ]);
        }

        return redirect()->route('animals.show', $animal->id)->with('success', 'Animal updated successfully.');
    }
}
