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
        $dams = Animal::where('gender', 'FEMALE')->where('is_active', true)->get();

        // Fetch possible Sires (Males)
        $sires = Animal::where('gender', 'MALE')->where('is_active', true)->get();

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
            'gender' => 'required|in:MALE,FEMALE',
            'initial_weight' => 'required|numeric|min:0.1',
            'breed_id' => 'required|exists:master_breeds,id',
            'category_id' => 'required|exists:master_categories,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'generation' => 'nullable|string',
            'necklace_color' => 'nullable|string',
        ]);

        // Auto-Inherit attributes from Dam
        $dam = Animal::find($validated['dam_id']);

        // 1. Check "Nifas" (Recovery) - Although form logic should prevent, we double check
        // Actually, this is a Birth, so Nifas applies to NEXT mating.
        // We might want to update Dam's status to LACTATING?
        // Yes, if she gave birth, she is likely Lactating or at least not Pregnant anymore.
        // Let's find "Menyusui" or "Lactating" status.
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
            'sire_id' => $validated['sire_id'],
            'category_id' => $validated['category_id'],
            'breed_id' => $validated['breed_id'],
            'current_location_id' => $validated['current_location_id'],
            'current_phys_status_id' => MasterPhysStatus::where('name', 'Cempe')->first()->id ?? 1,
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'acquisition_type' => 'BRED',
            'purchase_price' => 0,
            'generation' => $validated['generation'],
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
        // BreedingEvent::where('dam_id', $dam->id)->where('status', 'PENDING')->update(['status' => 'SUCCESS']);

        return redirect()->route('animals.index')->with('success', 'Kelahiran berhasil dicatat. Cempe baru telah ditambahkan.');
    }
}
