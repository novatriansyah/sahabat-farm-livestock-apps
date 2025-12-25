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

        // 5. Conception Rate (Updated Formula)
        // (Successful Pregnancies / (Total Completed Breedings - Pending)) * 100
        // "Completed Breedings" here implicitly means we exclude PENDING from the denominator?
        // Actually, user said: "Exclude 'PENDING' events... New Formula: (Successful / (Total Completed - Pending))"
        // Wait, "Total Completed - Pending"? If Total is ALL, then (Total - Pending) is Completed.
        // So: Success / (Total - Pending).
        $totalBreeding = BreedingEvent::count();
        $pendingBreeding = BreedingEvent::where('status', 'PENDING')->count();
        $successfulBreeding = BreedingEvent::where('status', 'SUCCESS')->count();

        $completedBreeding = $totalBreeding - $pendingBreeding;
        $conceptionRate = $completedBreeding > 0 ? ($successfulBreeding / $completedBreeding) * 100 : 0;

        // 6. Full Performance Stats
        // Feed Usage This Month (kg)
        $feedUsage = InventoryUsageLog::whereHas('item', function($q) {
                $q->where('category', 'FEED');
            })
            ->whereMonth('usage_date', Carbon::now()->month)
            ->sum('qty_used');

        // Medicine Usage Cost This Month (Est)
        $medicineLogs = InventoryUsageLog::whereHas('item', function($q) {
                $q->whereIn('category', ['MEDICINE', 'VITAMIN', 'VACCINE']);
            })
            ->whereMonth('usage_date', Carbon::now()->month)
            ->with('item')
            ->get();

        $medicineCost = 0;
        foreach ($medicineLogs as $log) {
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
        // Update check for 'Cempe Lahir' instead of 'Cempe' due to translation update
        $separationCandidates = Animal::where('is_active', true)
            ->whereDate('birth_date', '<=', Carbon::now()->subDays(60))
            ->whereHas('physStatus', function($q) {
                // We use LIKE or multiple checks to be safe, but exact name 'Cempe Lahir' is target.
                $q->where('name', 'Cempe Lahir');
            })
            ->get();

        // 10. Mating Separation Alerts (Pisah Pejantan)
        $matingSeparationCandidates = BreedingEvent::where('status', 'PENDING')
             ->whereDate('mating_date', '<=', Carbon::now()->subDays(60))
             ->with(['dam', 'sire'])
             ->get();

        // --- NEW CHARTS DATA ---

        // A. Mortality Trend (Last 6 Months)
        $mortalityTrendLabels = [];
        $mortalityTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $count = ExitLog::where('exit_type', 'DEATH')
                ->whereMonth('exit_date', $date->month)
                ->whereYear('exit_date', $date->year)
                ->count();
            $mortalityTrendLabels[] = $monthName;
            $mortalityTrendData[] = $count;
        }

        // B. Financial Summary (Last 6 Months: Sales Revenue vs Death Loss)
        $financialLabels = [];
        $financialRevenue = [];
        $financialLoss = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $financialLabels[] = $date->format('M Y');

            // Revenue
            $rev = ExitLog::where('exit_type', 'SALE')
                ->whereMonth('exit_date', $date->month)
                ->whereYear('exit_date', $date->year)
                ->sum('price');
            $financialRevenue[] = $rev;

            // Loss
            $loss = 0;
            $deaths = ExitLog::where('exit_type', 'DEATH')
                ->whereMonth('exit_date', $date->month)
                ->whereYear('exit_date', $date->year)
                ->with('animal')
                ->get();
            foreach ($deaths as $d) {
                $loss += ($d->animal->purchase_price ?? 0) + $d->final_hpp;
            }
            $financialLoss[] = $loss;
        }

        // C. Population Demographics (Pie Chart)
        $demographics = Animal::where('is_active', true)
            ->join('master_phys_statuses', 'animals.current_phys_status_id', '=', 'master_phys_statuses.id')
            ->select('master_phys_statuses.name', DB::raw('count(*) as total'))
            ->groupBy('master_phys_statuses.name')
            ->pluck('total', 'name')
            ->toArray();
        $demographicsLabels = array_keys($demographics);
        $demographicsData = array_values($demographics);

        // D. Expense Breakdown (This Month: Feed vs Medicine vs Others)
        // Feed Cost this month
        // We can estimate from usage logs logic
        // Feed:
        $feedLogs = InventoryUsageLog::whereHas('item', function($q) {
            $q->where('category', 'FEED');
        })->whereMonth('usage_date', Carbon::now()->month)->get();

        $totalFeedCost = 0;
        // Simple cache for prices to avoid N+1 inside loop (basic optimization)
        $priceCache = [];
        foreach ($feedLogs as $log) {
            if (!isset($priceCache[$log->item_id])) {
                $priceCache[$log->item_id] = DB::table('inventory_purchases')
                    ->where('item_id', $log->item_id)
                    ->selectRaw('SUM(price_total) / SUM(qty) as avg_price')
                    ->value('avg_price') ?? 0;
            }
            $totalFeedCost += ($log->qty_used + $log->qty_wasted) * $priceCache[$log->item_id];
        }

        // Medicine Cost (Already calculated as $medicineCost)
        $totalMedicineCost = $medicineCost;

        // Operational/Other? We don't have operational cost module yet. Just Feed vs Meds.
        $expenseLabels = ['Pakan (Feed)', 'Obat & Vitamin'];
        $expenseData = [$totalFeedCost, $totalMedicineCost];

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
            // Chart Data
            'mortalityTrendLabels' => $mortalityTrendLabels,
            'mortalityTrendData' => $mortalityTrendData,
            'financialLabels' => $financialLabels,
            'financialRevenue' => $financialRevenue,
            'financialLoss' => $financialLoss,
            'demographicsLabels' => $demographicsLabels,
            'demographicsData' => $demographicsData,
            'expenseLabels' => $expenseLabels,
            'expenseData' => $expenseData,
        ]);
    }
}
