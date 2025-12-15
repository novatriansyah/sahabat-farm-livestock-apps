<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\BreedingEvent;
use App\Models\ExitLog;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Models\MasterLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Live Population (Total & By Cage)
        $activeAnimals = Animal::where('is_active', true)->count();
        $populationByCage = MasterLocation::withCount(['animals' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        // 2. Growth Performance (Avg ADG)
        $avgAdg = Animal::where('is_active', true)->avg('daily_adg');

        // 3. Financials
        // Total Estimated Asset Value
        $totalStockValue = InventoryItem::sum(DB::raw('current_stock * (SELECT AVG(price_total/qty) FROM inventory_purchases WHERE item_id = inventory_items.id)'));
        if (!$totalStockValue) $totalStockValue = 0;

        // Sales This Month
        $salesThisMonth = ExitLog::where('exit_type', 'SALE')
            ->whereMonth('exit_date', Carbon::now()->month)
            ->whereYear('exit_date', Carbon::now()->year)
            ->sum('price');

        // Net Profit This Month
        $exits = ExitLog::where('exit_type', 'SALE')
            ->whereMonth('exit_date', Carbon::now()->month)
            ->whereYear('exit_date', Carbon::now()->year)
            ->with('animal')
            ->get();

        $netProfit = 0;
        foreach ($exits as $exit) {
            $purchasePrice = $exit->animal->purchase_price ?? 0;
            $cost = $purchasePrice + $exit->final_hpp;
            $netProfit += ($exit->price - $cost);
        }

        // 4. Low Stock Alerts (Threshold < 10 units)
        $lowStockItems = InventoryItem::where('current_stock', '<', 10)->get();

        // 5. Conception Rate (Successful Breeds / Total Breeds this month or all time?)
        // Let's do All Time for MVP to show data
        $totalBreeding = BreedingEvent::count();
        $successfulBreeding = BreedingEvent::where('status', 'SUCCESS')->count();

        $conceptionRate = $totalBreeding > 0 ? ($successfulBreeding / $totalBreeding) * 100 : 0;

        return view('dashboard', [
            'activeAnimals' => $activeAnimals,
            'populationByCage' => $populationByCage,
            'avgAdg' => $avgAdg,
            'totalStockValue' => $totalStockValue,
            'salesThisMonth' => $salesThisMonth,
            'netProfit' => $netProfit,
            'lowStockItems' => $lowStockItems,
            'conceptionRate' => $conceptionRate,
        ]);
    }
}
