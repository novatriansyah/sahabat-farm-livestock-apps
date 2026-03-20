<?php

namespace App\Http\Controllers;

use App\Models\HppManualCost;
use Illuminate\Http\Request;

class HppManualCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $costs = HppManualCost::orderBy('month', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        return view('finance.hpp', compact('costs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $cost = HppManualCost::create($request->all());

        // Distribute cost among currently active animals
        $activeAnimals = \App\Models\Animal::where('is_active', true)->count();
        if ($activeAnimals > 0) {
            $costPerHead = $cost->amount / $activeAnimals;
            \App\Models\Animal::where('is_active', true)->increment('current_hpp', $costPerHead);
        }

        return redirect()->back()->with('success', 'Biaya HPP berhasil ditambahkan dan didistribusikan ke ' . $activeAnimals . ' ekor ternak aktif.');
    }

    public function destroy(HppManualCost $hppManualCost)
    {
        // Revert HPP distribution
        $activeAnimals = \App\Models\Animal::where('is_active', true)->count();
        if ($activeAnimals > 0) {
            $costPerHead = $hppManualCost->amount / $activeAnimals;
            // Prevent negative HPP by capping it at 0 (handled roughly here by decrement)
            \App\Models\Animal::where('is_active', true)->decrement('current_hpp', $costPerHead);
        }
        
        $hppManualCost->delete();
        
        return redirect()->back()->with('success', 'Biaya HPP dihapus dan dikurangi dari ternak.');
    }
}
