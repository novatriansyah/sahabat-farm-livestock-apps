<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\BreedingEvent;
use App\Models\MasterPhysStatus;
use App\Services\BreedingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class BreedingController extends Controller
{
    protected $breedingService;

    public function __construct(BreedingService $breedingService)
    {
        $this->breedingService = $breedingService;
    }

    public function create(Animal $animal): View|RedirectResponse
    {
        // 1. Basic Checks
        if ($animal->gender !== 'FEMALE') {
            return back()->withErrors(['msg' => 'Only female animals can be bred.']);
        }

        // 2. Smart Validation
        $eligibility = $this->breedingService->isEligibleForMating($animal);

        // 3. Fetch Sires (Active Males of same breed/category)
        // Ideally same breed, but cross-breeding is allowed. Same Category (Sheep vs Goat) is strict.
        $sires = Animal::where('gender', 'MALE')
            ->where('is_active', true)
            ->where('category_id', $animal->category_id)
            ->get();

        return view('breeding.create', compact('animal', 'eligibility', 'sires'));
    }

    public function store(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'sire_id' => 'required|exists:animals,id',
            'mating_date' => 'required|date',
        ]);

        // Calculate Expected Birth Date (approx 150 days / 5 months)
        $matingDate = Carbon::parse($validated['mating_date']);
        $estBirthDate = $matingDate->copy()->addDays(150);

        // Create Event
        BreedingEvent::create([
            'dam_id' => $animal->id,
            'sire_id' => $validated['sire_id'],
            'mating_date' => $validated['mating_date'],
            'est_birth_date' => $estBirthDate,
            'status' => 'PENDING',
        ]);

        // Update Animal Status to PREGNANT?
        // Usually we wait for confirmation (ultrasound/no return to estrus).
        // But for this MVP flow, maybe we just mark her as 'MATED'?
        // Let's check our Enums. We have 'PREGNANT', 'READY_TO_MATE'.
        // Let's leave status as is for now, or maybe move to 'PREGNANT' if the user is confident.
        // For now, just logging the event is key for the Conception Rate.

        return redirect()->route('animals.index')->with('success', 'Mating recorded successfully.');
    }
}
