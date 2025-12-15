<?php

namespace App\Http\Controllers;

use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterDisease;
use App\Models\MasterLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MasterDataController extends Controller
{
    public function index(): View
    {
        $breeds = MasterBreed::with('category')->paginate(10);
        $locations = MasterLocation::paginate(10);
        $diseases = MasterDisease::paginate(10);

        return view('admin.masters.index', compact('breeds', 'locations', 'diseases'));
    }

    public function storeBreed(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:master_categories,id',
            'origin' => 'nullable|string',
            'min_weight_mate' => 'nullable|numeric',
            'min_age_mate_months' => 'nullable|integer',
        ]);

        MasterBreed::create($validated);
        return back()->with('success', 'Breed added successfully.');
    }

    public function storeLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        MasterLocation::create($validated);
        return back()->with('success', 'Location added successfully.');
    }

    public function storeDisease(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symptoms' => 'nullable|string',
        ]);

        MasterDisease::create($validated);
        return back()->with('success', 'Disease added successfully.');
    }
}
