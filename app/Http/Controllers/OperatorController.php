<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use App\Models\InventoryUsageLog;
use App\Models\MasterDisease;
use App\Models\MasterLocation;
use App\Models\TreatmentLog;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class OperatorController extends Controller
{
    public function show(Animal $animal): View
    {
        $medicines = InventoryItem::where('category', 'MEDICINE')
            ->orWhere('category', 'VITAMIN')
            ->orWhere('category', 'VACCINE')
            ->get();

        $diseases = MasterDisease::all();
        $locations = MasterLocation::all();

        return view('operator.show', compact('animal', 'medicines', 'diseases', 'locations'));
    }

    public function storeWeight(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'weight_kg' => 'required|numeric|min:0',
        ]);

        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => Carbon::now(),
            'weight_kg' => $validated['weight_kg'],
        ]);

        return redirect()->route('operator.show', $animal->id)->with('success', 'Weight recorded.');
    }

    public function storeHealth(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'health_status' => 'required|in:HEALTHY,SICK,QUARANTINE',
            'disease_id' => 'nullable|exists:master_diseases,id',
            'symptoms' => 'nullable|string',
            'medicine_id' => 'nullable|exists:inventory_items,id',
            'medicine_qty' => 'nullable|required_with:medicine_id|numeric|min:0',
        ]);

        $animal->update(['health_status' => $validated['health_status']]);

        // Get Diagnosis Name if disease_id present
        $diagnosis = null;
        if ($request->filled('disease_id')) {
            $diagnosis = MasterDisease::find($validated['disease_id'])->name;
        }

        if ($validated['health_status'] !== 'HEALTHY' || $request->filled('medicine_id')) {
            TreatmentLog::create([
                'animal_id' => $animal->id,
                'treatment_date' => Carbon::now(),
                'type' => $request->filled('medicine_id') ? 'MEDICATION' : 'CHECKUP',
                'notes' => ($diagnosis ? "Diagnosis: $diagnosis. " : '') . ($validated['symptoms'] ?? 'Health Check'),
            ]);
        }

        if ($request->filled('medicine_id')) {
            $item = InventoryItem::findOrFail($validated['medicine_id']);

            if ($item->current_stock >= $validated['medicine_qty']) {
                 $item->decrement('current_stock', $validated['medicine_qty']);

                 InventoryUsageLog::create([
                     'item_id' => $item->id,
                     'location_id' => $animal->current_location_id,
                     'usage_date' => Carbon::now(),
                     'qty_used' => $validated['medicine_qty'],
                     'qty_wasted' => 0,
                 ]);

                 // Calculate Cost immediately for Medicines
                 $avgPrice = $this->getItemPrice($item->id);
                 $cost = $validated['medicine_qty'] * $avgPrice;

                 $animal->increment('accumulated_medicine_cost', $cost);
                 $animal->increment('current_hpp', $cost);

            } else {
                return back()->withErrors(['medicine_qty' => 'Not enough stock.']);
            }
        }

        return redirect()->route('operator.show', $animal->id)->with('success', 'Health status updated.');
    }

    public function moveCage(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:master_locations,id',
        ]);

        $animal->update(['current_location_id' => $validated['location_id']]);

        return redirect()->route('operator.show', $animal->id)->with('success', 'Cage moved successfully.');
    }

    private function getItemPrice($itemId): float
    {
        $totalValue = InventoryPurchase::where('item_id', $itemId)->sum('price_total');
        $totalQty = InventoryPurchase::where('item_id', $itemId)->sum('qty');

        return ($totalQty > 0) ? ($totalValue / $totalQty) : 0;
    }
}
