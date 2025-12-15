<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::paginate(10);
        return view('inventory.index', compact('items'));
    }

    public function show(InventoryItem $inventory): View // Route param is usually 'inventory' for resource
    {
        // Actually route is 'inventory/{inventory}' but model binding works if matched.
        // Let's assume route resource uses 'inventory' param.
        return view('inventory.show', ['item' => $inventory]);
    }

    public function edit(InventoryItem $inventory): View
    {
        return view('inventory.edit', ['item' => $inventory]);
    }

    public function update(Request $request, InventoryItem $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'category' => 'required|in:MEDICINE,VITAMIN,VACCINE,FEED',
            'dosage_per_kg' => 'nullable|numeric',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully.');
    }
}
