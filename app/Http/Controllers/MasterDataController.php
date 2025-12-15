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

    // --- BREED ---
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

    public function editBreed(MasterBreed $breed): View
    {
        $categories = MasterCategory::all();
        return view('admin.masters.edit-breed', compact('breed', 'categories'));
    }

    public function updateBreed(Request $request, MasterBreed $breed): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:master_categories,id',
            'origin' => 'nullable|string',
            'min_weight_mate' => 'nullable|numeric',
            'min_age_mate_months' => 'nullable|integer',
        ]);

        $breed->update($validated);
        return redirect()->route('masters.index')->with('success', 'Breed updated.');
    }

    // --- LOCATION ---
    public function storeLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        MasterLocation::create($validated);
        return back()->with('success', 'Location added successfully.');
    }

    public function editLocation(MasterLocation $location): View
    {
        return view('admin.masters.edit-location', compact('location'));
    }

    public function updateLocation(Request $request, MasterLocation $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $location->update($validated);
        return redirect()->route('masters.index')->with('success', 'Location updated.');
    }

    // --- DISEASE ---
    public function storeDisease(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symptoms' => 'nullable|string',
        ]);

        MasterDisease::create($validated);
        return back()->with('success', 'Disease added successfully.');
    }

    public function editDisease(MasterDisease $disease): View
    {
        return view('admin.masters.edit-disease', compact('disease'));
    }

    public function updateDisease(Request $request, MasterDisease $disease): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symptoms' => 'nullable|string',
        ]);

        $disease->update($validated);
        return redirect()->route('masters.index')->with('success', 'Disease updated.');
    }

    // --- CATEGORY ---
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MasterCategory::create($validated);
        return back()->with('success', 'Category added successfully.');
    }

    public function editCategory(MasterCategory $category): View
    {
        return view('admin.masters.edit-category', compact('category'));
    }

    public function updateCategory(Request $request, MasterCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);
        return redirect()->route('masters.index')->with('success', 'Category updated.');
    }

    // --- ITEM ---
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
}
