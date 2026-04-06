<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\MasterCategory;
use App\Models\MasterBreed;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\MasterPartner;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use App\Services\BreedingService;

class BirthController extends Controller
{
    public function create(): View
    {
        // Fetch possible Dams (Females) - ideally Pregnant ones, but lets list all active females for flexibility
        $dams = Animal::where('gender', 'BETINA')->where('is_active', true)->with('breed')->get();

        // Fetch possible Sires (Males)
        $sires = Animal::where('gender', 'JANTAN')->where('is_active', true)->with('breed')->get();

        $categories = MasterCategory::all();
        $breeds = MasterBreed::all();
        $locations = MasterLocation::all();
        $partners = MasterPartner::all();

        // Status for Newborn = 'Cempe'
        $cempeStatus = MasterPhysStatus::where('name', 'Cempe')->first();
        $allStatuses = MasterPhysStatus::all();

        return view('animals.birth.create', compact('dams', 'sires', 'categories', 'breeds', 'locations', 'cempeStatus', 'allStatuses', 'partners'));
    }

    public function store(Request $request, \App\Services\TaskService $taskService): RedirectResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|unique:animals',
            'dam_id' => 'required|exists:animals,id',
            'sire_id' => 'nullable|exists:animals,id',
            'partner_id' => 'nullable|exists:master_partners,id',
            'birth_date' => 'required|date',
            'gender' => 'required|in:JANTAN,BETINA',
            'initial_weight' => 'required|numeric|min:0.1',
            'breed_id' => 'required_without:sire_id|nullable|exists:master_breeds,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'generation' => 'nullable|string',
            'necklace_color' => 'nullable|string',
        ]);

        // Auto-Inherit attributes from Dam & Sire
        $dam = Animal::find($validated['dam_id']);
        
        $generation = $validated['generation'];
        $breedId = $validated['breed_id'];

        if (isset($validated['sire_id']) && !empty($validated['sire_id'])) {
            $sire = Animal::find($validated['sire_id']);
            $breedId = $sire->breed_id; // Auto-detect breed from Male parent
            
            // Mathematically calculate generation (F+1)
            $sGen = $sire->generation;
            $dGen = $dam->generation;

            // Extract Numbers from F1, F2...
            preg_match('/F(\d+)/i', $sGen, $sMatch);
            preg_match('/F(\d+)/i', $dGen, $dMatch);
            
            $sNum = !empty($sMatch[1]) ? (int)$sMatch[1] : 0;
            $dNum = !empty($dMatch[1]) ? (int)$dMatch[1] : 0;
            
            // PUREBREED/PURE logic (treated as F6)
            if (in_array(strtoupper($sGen), ['PURE', 'PUREBREED', 'PB'])) $sNum = 6;
            if (in_array(strtoupper($dGen), ['PURE', 'PUREBREED', 'PB'])) $dNum = 6;
            
            // Logic: max(parents) + 1
            $next = max($sNum, $dNum) + 1;
            $generation = ($next >= 6) ? 'PURE' : 'F' . $next;
        } else {
            // If no sire, fallback to Dam's generation or default F1 if Dam is Lokal
            if (!$generation) {
                preg_match('/F(\d+)/i', $dam->generation, $dMatch);
                $dNum = !empty($dMatch[1]) ? (int)$dMatch[1] : 0;
                $next = $dNum + 1;
                $generation = ($next >= 6) ? 'PURE' : 'F' . $next;
            }
        }
        
        // Auto-detect category from Breed
        $breed = MasterBreed::find($breedId);
        $categoryId = $breed ? $breed->category_id : null;

        // Partner inheritance logic
        // If Dam has a partner, offspring MUST belong to that partner.
        // Otherwise, use the manually selected partner (or null if "Tidak Diketahui")
        $finalPartnerId = $dam->partner_id ?? $validated['partner_id'];

        // 1. Update Dam Status to Lactating
        $lactating = MasterPhysStatus::where('is_lactating', true)->first();
        if ($lactating) {
            $dam->update(['current_phys_status_id' => $lactating->id]);
        }

        // 2. Create Offspring
        $cempeStatus = MasterPhysStatus::where('name', 'like', '%Cempe%')->first();
        $offspring = Animal::create([
            'tag_id' => $validated['tag_id'],
            'owner_id' => auth()->id(), // System User
            'partner_id' => $finalPartnerId,
            'dam_id' => $dam->id,
            'sire_id' => $validated['sire_id'] ?? null,
            'category_id' => $categoryId,
            'breed_id' => $breedId,
            'current_location_id' => $validated['current_location_id'],
            'current_phys_status_id' => $cempeStatus->id ?? 1,
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'acquisition_type' => 'HASIL_TERNAK',
            'purchase_price' => 0,
            'generation' => $generation,
            'necklace_color' => $validated['necklace_color'],
            'is_active' => true,
        ]);

        // 3. Log Weight
        WeightLog::create([
            'animal_id' => $offspring->id,
            'weigh_date' => Carbon::now(),
            'weight_kg' => $validated['initial_weight'],
        ]);

        // 4. Generate SOP Tasks
        $taskService->generateBirthTasks($offspring);

        return redirect()->route('animals.index')->with('success', 'Kelahiran berhasil dicatat. Cempe baru telah ditambahkan dan tugas SOP telah dibuat.');
    }
}
