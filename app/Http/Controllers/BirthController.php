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
        $dams = Animal::where('gender', 'BETINA')->where('is_active', true)->get();

        // Fetch possible Sires (Males)
        $sires = Animal::where('gender', 'JANTAN')->where('is_active', true)->get();

        $categories = MasterCategory::all();
        $breeds = MasterBreed::all();
        $locations = MasterLocation::all();

        // Status for Newborn = 'Cempe'
        $cempeStatus = MasterPhysStatus::where('name', 'Cempe')->first();
        $allStatuses = MasterPhysStatus::all();

        return view('animals.birth.create', compact('dams', 'sires', 'categories', 'breeds', 'locations', 'cempeStatus', 'allStatuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|unique:animals',
            'dam_id' => 'required|exists:animals,id',
            'sire_id' => 'nullable|exists:animals,id',
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
            
            // Mathematically calculate generation (F1 + F1 = F2) capping at F6 (PURE)
            $sGen = $sire->generation;
            $dGen = $dam->generation;

            if ($sGen && $dGen) {
                preg_match('/F(\d+)/i', $sGen, $sMatch);
                preg_match('/F(\d+)/i', $dGen, $dMatch);
                
                $sNum = !empty($sMatch[1]) ? (int)$sMatch[1] : 0;
                $dNum = !empty($dMatch[1]) ? (int)$dMatch[1] : 0;
                
                if (in_array(strtoupper($sGen), ['PURE', 'PUREBREED'])) $sNum = 6;
                if (in_array(strtoupper($dGen), ['PURE', 'PUREBREED'])) $dNum = 6;
                
                if ($sNum > 0 && $dNum > 0) {
                    $next = max($sNum, $dNum) + 1;
                    $generation = $next >= 6 ? 'PURE' : 'F' . $next;
                } elseif (in_array(strtoupper($sGen), ['PURE', 'PUREBREED']) && in_array(strtoupper($dGen), ['PURE', 'PUREBREED'])) {
                    $generation = 'PURE';
                }
            }
        }
        
        // Auto-detect category from Breed
        $breed = MasterBreed::find($breedId);
        $categoryId = $breed ? $breed->category_id : null;

        // 1. Check "Nifas" (Recovery)
        $lactating = MasterPhysStatus::where('name', 'Lactating')->orWhere('name', 'Menyusui')->first();
        if ($lactating) {
            $dam->update(['current_phys_status_id' => $lactating->id]);
        }

        // 2. Create Offspring
        $offspring = Animal::create([
            'tag_id' => $validated['tag_id'],
            'owner_id' => auth()->id(), // System User
            'partner_id' => $dam->partner_id, // Inherit Ownership
            'dam_id' => $dam->id,
            'sire_id' => $validated['sire_id'] ?? null,
            'category_id' => $categoryId,
            'breed_id' => $breedId,
            'current_location_id' => $validated['current_location_id'],
            'current_phys_status_id' => MasterPhysStatus::where('name', 'Cempe')->first()->id ?? 1,
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

        // 4. Update Breeding Event if exists?
        // If we tracked Pregnancy via BreedingEvent, we should close it as SUCCESS.
        // Finding the latest PENDING breeding event for this Dam.
        // (Assuming BreedingEvent model exists from previous context)
        // BreedingEvent::where('dam_id', $dam->id)->where('status', 'MENUNGGU')->update(['status' => 'BERHASIL']);

        return redirect()->route('animals.index')->with('success', 'Kelahiran berhasil dicatat. Cempe baru telah ditambahkan.');
    }
}
