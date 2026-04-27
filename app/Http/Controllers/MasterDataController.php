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
        $diseases = MasterDisease::with('recommendedTreatments')->paginate(10, ['*'], 'diseases_page');
        $items = InventoryItem::paginate(10, ['*'], 'items_page');
        $categories = MasterCategory::paginate(10, ['*'], 'categories_page');
        $sops = \App\Models\MasterSop::paginate(10, ['*'], 'sops_page');
        $settings = \App\Models\FarmSetting::all()->groupBy('group');

        return view('admin.masters.index', compact('breeds', 'locations', 'diseases', 'items', 'categories', 'sops', 'settings'));
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
        return back()->with('success', 'Ras/Bangsa berhasil ditambahkan.');
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
        return redirect()->route('masters.index')->with('success', 'Ras/Bangsa berhasil diperbarui.');
    }

    // --- LOCATION ---
    public function storeLocation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        MasterLocation::create($validated);
        return back()->with('success', 'Lokasi berhasil ditambahkan.');
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
        return redirect()->route('masters.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    // --- DISEASE ---
    public function storeDisease(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:master_diseases,name',
            'symptoms' => 'nullable|string',
            'description' => 'nullable|string',
            'treatments' => 'nullable|array',
            'treatments.*' => 'exists:inventory_items,id',
            'custom_dosages' => 'nullable|array',
        ]);

        $disease = MasterDisease::create($validated);

        if ($request->has('treatments')) {
            $syncData = [];
            foreach ($validated['treatments'] as $itemId) {
                $syncData[$itemId] = [
                    'custom_dosage' => $validated['custom_dosages'][$itemId] ?? null
                ];
            }
            $disease->recommendedTreatments()->sync($syncData);
        }

        return back()->with('success', 'Penyakit berhasil ditambahkan.');
    }

    public function editDisease(MasterDisease $disease): View
    {
        $disease->load('recommendedTreatments');
        $items = InventoryItem::whereIn('category', ['Obat-Obatan', 'Vitamin', 'Vaksin'])->get();
        return view('admin.masters.edit-disease', compact('disease', 'items'));
    }

    public function updateDisease(Request $request, MasterDisease $disease): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:master_diseases,name,' . $disease->id,
            'symptoms' => 'nullable|string',
            'description' => 'nullable|string',
            'treatments' => 'nullable|array',
            'treatments.*' => 'exists:inventory_items,id',
            'custom_dosages' => 'nullable|array',
        ]);

        $disease->update($validated);

        if ($request->has('treatments')) {
            $syncData = [];
            foreach ($validated['treatments'] as $itemId) {
                $syncData[$itemId] = [
                    'custom_dosage' => $validated['custom_dosages'][$itemId] ?? null
                ];
            }
            $disease->recommendedTreatments()->sync($syncData);
        } else {
            $disease->recommendedTreatments()->detach();
        }

        return redirect()->route('admin.masters.index', ['tab' => 'diseases'])->with('success', 'Penyakit berhasil diperbarui.');
    }

    // --- CATEGORY ---
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MasterCategory::create($validated);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
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
        return redirect()->route('masters.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // --- ITEM ---
    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'category' => 'required|in:Obat-Obatan,Vitamin,Vaksin,Pakan',
            'dosage_per_kg' => 'nullable|numeric',
        ]);

        InventoryItem::create($validated);
        return back()->with('success', 'Barang inventaris berhasil ditambahkan.');
    }

    // --- SOP ---
    public function storeSop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
            'title' => 'required|string|max:255',
            'task_type' => 'required|string',
            'due_days_offset' => 'required|integer|min:0',
        ]);

        \App\Models\MasterSop::create($validated);
        return back()->with('success', 'SOP Tugas berhasil ditambahkan.');
    }

    public function updateSop(Request $request, \App\Models\MasterSop $sop): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
            'title' => 'required|string|max:255',
            'task_type' => 'required|string',
            'due_days_offset' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $sop->update($validated);
        return back()->with('success', 'SOP Tugas berhasil diperbarui.');
    }

    public function destroySop(\App\Models\MasterSop $sop): RedirectResponse
    {
        $sop->delete();
        return back()->with('success', 'SOP Tugas berhasil dihapus.');
    }

    // --- SETTINGS ---
    public function updateSettings(Request $request): RedirectResponse
    {
        $allowedKeys = [
            'gestation_period_days', 'nifas_period_days', 'weaning_age_days',
            'pregnancy_check_days', 'separation_age_days', 'kid_threshold_days',
            'low_stock_threshold', 'default_invoice_due_days', 'min_age_mate_months_fallback',
            'min_weight_mate_fallback', 'est_feed_cost_day', 'est_health_cost_month',
            'est_ops_cost_month', 'vaccine_alert_days', 'mating_colony_days',
            'adg_performance_threshold'
        ];

        foreach ($request->input('settings', []) as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                \App\Models\FarmSetting::where('key', $key)->update(['value' => $value]);
            }
        }

        return back()->with('success', 'Pengaturan peternakan berhasil diperbarui.');
    }
}
