<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class InventoryUsageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'location_id' => 'nullable|exists:master_locations,id',
            'qty_used' => 'required|numeric|min:0',
            'qty_wasted' => 'required|numeric|min:0',
            'usage_date' => 'required|date',
        ]);

        // Check stock
        $item = InventoryItem::findOrFail($validated['item_id']);
        if ($item->current_stock < ($validated['qty_used'] + $validated['qty_wasted'])) {
            return back()->withErrors(['qty_used' => 'Not enough stock.']);
        }

        InventoryUsageLog::create($validated);

        // Update Stock
        $item->decrement('current_stock', $validated['qty_used'] + $validated['qty_wasted']);

        return redirect()->route('inventory.index')->with('success', 'Usage recorded successfully.');
    }
}
