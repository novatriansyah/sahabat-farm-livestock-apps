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
            // final_hpp in ExitLog is snapshot of current_hpp (Feed+Meds)
            $cost = $purchasePrice + $exit->final_hpp;
            $netProfit += ($exit->price - $cost);
        }

        // 4. Low Stock Alerts
        $lowStockItems = InventoryItem::where('current_stock', '<', 10)->get();

        // 5. Conception Rate
        $totalBreeding = BreedingEvent::count();
        $successfulBreeding = BreedingEvent::where('status', 'SUCCESS')->count();
        $conceptionRate = $totalBreeding > 0 ? ($successfulBreeding / $totalBreeding) * 100 : 0;

        // 6. Full Performance Stats
        // Feed Usage This Month (kg)
        $feedUsage = InventoryUsageLog::whereHas('item', function($q) {
                $q->where('category', 'FEED');
            })
            ->whereMonth('usage_date', Carbon::now()->month)
            ->sum('qty_used');

        // Medicine Usage Cost This Month (Est)
        // We need to sum cost of med usage.
        // We can use the logic from OperatorController or HPP calc.
        // For dashboard, we can query logs where category=MEDICINE/VACCINE/VITAMIN
        // and multiply by avg price.
        $medicineLogs = InventoryUsageLog::whereHas('item', function($q) {
                $q->whereIn('category', ['MEDICINE', 'VITAMIN', 'VACCINE']);
            })
            ->whereMonth('usage_date', Carbon::now()->month)
            ->with('item')
            ->get();

        $medicineCost = 0;
        foreach ($medicineLogs as $log) {
            // Re-calculate price (simplified, ideally usage log should store cost snapshot)
            // But for MVP dashboard, dynamic calc is fine.
            $avgPrice = DB::table('inventory_purchases')
                ->where('item_id', $log->item_id)
                ->selectRaw('SUM(price_total) / SUM(qty) as avg_price')
                ->value('avg_price') ?? 0;

            $medicineCost += ($log->qty_used + $log->qty_wasted) * $avgPrice;
        }

        // Mortality Stats This Month
        $deathCount = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', Carbon::now()->month)
            ->count();

        // Lost Asset Value (Purchase Price + Accumulated Costs)
        $deathValue = 0;
        $deadAnimals = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', Carbon::now()->month)
            ->with('animal')
            ->get();

        foreach ($deadAnimals as $log) {
            $deathValue += ($log->animal->purchase_price ?? 0) + $log->final_hpp;
        }

        // 7. Breakdown by Sex (Live)
        $liveMale = Animal::where('is_active', true)->where('gender', 'MALE')->count();
        $liveFemale = Animal::where('is_active', true)->where('gender', 'FEMALE')->count();

        // 8. Breakdown by Sex (Dead - This Month)
        $deadMale = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', Carbon::now()->month)
            ->whereHas('animal', function($q) { $q->where('gender', 'MALE'); })
            ->count();
        $deadFemale = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', Carbon::now()->month)
            ->whereHas('animal', function($q) { $q->where('gender', 'FEMALE'); })
            ->count();

        // 9. Separation Alerts (Pisah Koloni)
        // Animals > 2 months old (60 days) who are still 'Cempe' or have not been weaned manually.
        // Assuming 'Cempe' status ID or Name.
        $separationCandidates = Animal::where('is_active', true)
            ->whereDate('birth_date', '<=', Carbon::now()->subDays(60))
            ->whereHas('physStatus', function($q) {
                $q->where('name', 'Cempe'); // Assuming 'Cempe' is the status name
            })
            ->get();

        // 10. Mating Separation Alerts (Pisah Pejantan)
        // Breeding events active > 60 days
        $matingSeparationCandidates = BreedingEvent::where('status', 'PENDING')
             ->whereDate('mating_date', '<=', Carbon::now()->subDays(60))
             ->with(['dam', 'sire'])
             ->get();

        return view('dashboard', [
            'activeAnimals' => $activeAnimals,
            'populationByCage' => $populationByCage,
            'avgAdg' => $avgAdg,
            'totalStockValue' => $totalStockValue,
            'salesThisMonth' => $salesThisMonth,
            'netProfit' => $netProfit,
            'lowStockItems' => $lowStockItems,
            'conceptionRate' => $conceptionRate,
            'feedUsage' => $feedUsage,
            'medicineCost' => $medicineCost,
            'deathCount' => $deathCount,
            'deathValue' => $deathValue,
            'liveMale' => $liveMale,
            'liveFemale' => $liveFemale,
            'deadMale' => $deadMale,
            'deadFemale' => $deadFemale,
            'separationCandidates' => $separationCandidates,
            'matingSeparationCandidates' => $matingSeparationCandidates,
        ]);
    }
}
