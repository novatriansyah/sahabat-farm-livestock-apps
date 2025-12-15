<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
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
        $breeds = MasterBreed::with('category')->paginate(10, ['*'], 'breeds_page');
        $locations = MasterLocation::paginate(10, ['*'], 'locations_page');
        $diseases = MasterDisease::paginate(10, ['*'], 'diseases_page');
        $items = InventoryItem::paginate(10, ['*'], 'items_page');
        $categories = MasterCategory::paginate(10, ['*'], 'categories_page');

        return view('admin.masters.index', compact('breeds', 'locations', 'diseases', 'items', 'categories'));
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

    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'category' => 'required|in:MEDICINE,VITAMIN,VACCINE,FEED',
            'dosage_per_kg' => 'nullable|numeric',
        ]);

        InventoryItem::create($validated);
        return back()->with('success', 'Inventory Item added successfully.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MasterCategory::create($validated);
        return back()->with('success', 'Category added successfully.');
    }
}
