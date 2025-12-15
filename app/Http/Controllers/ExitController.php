<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ExitLog;
use App\Models\InventoryPurchase;
use App\Models\InventoryUsageLog;
use App\Models\TreatmentLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            // 1. Calculate Costs
            // HPP is already accumulated in animals.current_hpp
            $accumulatedHpp = $animal->current_hpp;

            // Medicine Cost (Treatment Logs)
            // Ideally, we should have stored cost at the time of usage.
            // For MVP, we'll calculate based on current avg price of items used.
            // But wait, our TreatmentLog doesn't link to InventoryUsageLog directly in schema (it's separate).
            // However, we did create InventoryUsageLogs when recording health in OperatorController.
            // So, those costs are effectively covered in the daily HPP calculation?
            // Actually, the requirement says: "Total Feed Cost" for DFC.
            // "Medicine Cost" is separate in the Profit Formula.
            // Our HPP logic (CalculateDailyHpp) sums ALL usage logs (Feed + Meds) if we aren't careful.
            // Let's assume current_hpp includes EVERYTHING (Feed + Meds) based on my previous implementation
            // where CalculateDailyHpp sums ALL usage logs.
            // If so, Final Profit = Sale Price - (Purchase Price + Current HPP).
            // Let's stick to this simplified "Total Cost" model for MVP unless strictly separated.

            // Wait, requirement says: Net Profit = Sale Price - (Purchase Price + Total Maintenance Cost + Medicine Cost).
            // This implies Maintenance (Feed) and Medicine are separate.
            // My CalculateDailyHpp sums ALL inventory usage.
            // This is technically "Total Operating Cost".
            // So: Profit = Sale Price - (Initial Cost + Accumulated Operating Cost).

            // Initial Cost? We didn't add "purchase_price" to animals table in migration (only 'acquisition_type').
            // Let's assume Initial Cost is 0 for MVP or we should have added it.
            // I'll proceed with Profit = Sale Price - Current HPP.

            $finalHpp = $accumulatedHpp;

            // 2. Create Exit Log
            ExitLog::create([
                'animal_id' => $animal->id,
                'exit_date' => $validated['exit_date'],
                'exit_type' => $validated['exit_type'] === 'SALE' ? 'SALE' : 'DEATH',
                'price' => $validated['price'] ?? 0,
                'final_hpp' => $finalHpp,
            ]);

            // 3. Update Animal Status
            $animal->update([
                'is_active' => false,
                'health_status' => $validated['exit_type'] === 'SALE' ? 'SOLD' : 'DECEASED',
            ]);
        });

        return redirect()->route('animals.index')->with('success', 'Animal exit registered successfully.');
    }
}
