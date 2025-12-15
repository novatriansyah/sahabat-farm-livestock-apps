<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\MasterCategory;
use App\Models\MasterBreed;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AnimalController extends Controller
{
    public function index(): View
    {
        $animals = Animal::with(['category', 'breed', 'location', 'physStatus'])->paginate(10);
        return view('animals.index', compact('animals'));
    }

    public function create(): View
    {
        $categories = MasterCategory::all();
        $breeds = MasterBreed::all();
        $locations = MasterLocation::all();
        $statuses = MasterPhysStatus::all();
        $owners = User::all(); // Simplified for MVP

        return view('animals.create', compact('categories', 'breeds', 'locations', 'statuses', 'owners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tag_id' => 'required|unique:animals',
            'owner_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:master_categories,id',
            'breed_id' => 'required|exists:master_breeds,id',
            'current_location_id' => 'required|exists:master_locations,id',
            'current_phys_status_id' => 'required|exists:master_phys_statuses,id',
            'gender' => 'required|in:MALE,FEMALE',
            'birth_date' => 'required|date',
            'acquisition_type' => 'required|in:BRED,BOUGHT',
        ]);

        Animal::create($validated);

        return redirect()->route('animals.index')->with('success', 'Animal created successfully.');
    }
}
