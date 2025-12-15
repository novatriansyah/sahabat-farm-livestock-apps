<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class InventoryPurchaseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'date' => 'required|date',
            'qty' => 'required|numeric|min:0.01',
            'price_total' => 'required|numeric|min:0',
        ]);

        // Create Purchase Record
        InventoryPurchase::create($validated);

        // Increment Stock
        $item = InventoryItem::findOrFail($validated['item_id']);
        $item->increment('current_stock', $validated['qty']);

        return redirect()->route('inventory.index')->with('success', 'Purchase recorded and stock updated.');
    }
}
