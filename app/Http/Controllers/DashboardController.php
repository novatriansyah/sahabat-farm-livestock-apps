<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Summary Stats
        $activeAnimals = Animal::where('is_active', true)->count();
        $totalStockValue = InventoryItem::sum(DB::raw('current_stock * (SELECT AVG(price_total/qty) FROM inventory_purchases WHERE item_id = inventory_items.id)'));
        // Fallback if no purchases yet
        if (!$totalStockValue) $totalStockValue = 0;

        // Chart Data: Total Asset Value vs Monthly Feed Cost (Mock Data for now or simple calculation)
        // For MVP demo, we can just pass some data or calculate real if possible.

        $feedCost = InventoryUsageLog::whereMonth('usage_date', Carbon::now()->month)
            ->join('inventory_items', 'inventory_usage_logs.item_id', '=', 'inventory_items.id')
            // This is complex SQL for MVP, let's keep it simple for the view
            ->count();

        return view('dashboard', [
            'activeAnimals' => $activeAnimals,
            'totalStockValue' => $totalStockValue,
        ]);
    }
}
