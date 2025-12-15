<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ExitLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ExitController extends Controller
{
    public function create(Animal $animal): View
    {
        return view('animals.exit', compact('animal'));
    }

    public function store(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'exit_type' => 'required|in:SALE,DEATH',
            'exit_date' => 'required|date',
            'price' => 'nullable|numeric|min:0', // Required if SALE
            'notes' => 'nullable|string',
        ]);

        if ($validated['exit_type'] === 'SALE' && empty($validated['price'])) {
            return back()->withErrors(['price' => 'Price is required for sales.']);
        }

        DB::transaction(function () use ($request, $animal, $validated) {
            // Profit Formula: Sale Price - (Purchase Price + Accumulated Feed + Accumulated Medicine)
            // Note: We store "Final HPP" in ExitLog as the sum of Feed + Medicine for simplified reporting,
            // but the dashboard logic calculates Net Profit using Purchase Price too.
            // current_hpp = feed + medicine.

            $finalHpp = $animal->current_hpp;

            // Create Exit Log
            ExitLog::create([
                'animal_id' => $animal->id,
                'exit_date' => $validated['exit_date'],
                'exit_type' => $validated['exit_type'] === 'SALE' ? 'SALE' : 'DEATH',
                'price' => $validated['price'] ?? 0,
                'final_hpp' => $finalHpp,
            ]);

            // Update Animal Status
            $animal->update([
                'is_active' => false,
                'health_status' => $validated['exit_type'] === 'SALE' ? 'SOLD' : 'DECEASED',
            ]);
        });

        return redirect()->route('animals.index')->with('success', 'Animal exit registered successfully.');
    }
}
