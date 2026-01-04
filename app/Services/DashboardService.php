<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\BreedingEvent;
use App\Models\ExitLog;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Models\MasterLocation;
use App\Models\TreatmentLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(?int $filterPartnerId)
    {
        $cacheKey = 'dashboard_stats_' . ($filterPartnerId ?? 'global');
        // Cache for 10 minutes (600 seconds)
        // NOTE: 'file' driver does not support tags. We fallback to standard key.
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($filterPartnerId) {
            
            // Helper to scope queries
            $scopePartner = function ($query) use ($filterPartnerId) {
                if ($filterPartnerId) {
                    $query->where('partner_id', $filterPartnerId);
                }
            };

            $scopeAnimalRelation = function ($query) use ($filterPartnerId) {
                if ($filterPartnerId) {
                     // Check if 'whereHas' or 'join' is better. 
                     // For scope reusable, 'whereHas' is easier but slower. 
                     // We will keep this helper but inside the logic we prefer JOINs where explicit.
                    $query->whereHas('animal', function ($q) use ($filterPartnerId) {
                        $q->where('partner_id', $filterPartnerId);
                    });
                }
            };

            // 1. Live Population (Total & By Cage)
            // Use Index-Friendly Count
            $activeAnimals = Animal::where('is_active', true)
                ->when($filterPartnerId, $scopePartner)
                ->count();
                
            $populationByCage = MasterLocation::withCount(['animals' => function ($query) use ($filterPartnerId) {
                $query->where('is_active', true);
                if ($filterPartnerId) {
                    $query->where('partner_id', $filterPartnerId);
                }
            }])->get();

            // 2. Growth Performance (Avg ADG)
            $avgAdg = Animal::where('is_active', true)
                 ->when($filterPartnerId, $scopePartner)
                 ->avg('daily_adg') ?? 0;

            // 3. Financials
            $totalStockValue = 0;
            if (!$filterPartnerId) {
                 $totalStockValue = InventoryItem::sum(DB::raw('current_stock * (SELECT AVG(price_total/qty) FROM inventory_purchases WHERE item_id = inventory_items.id)'));
            }

            // Sales This Month
            $salesThisMonth = ExitLog::where('exit_type', 'SALE')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->whereYear('exit_date', Carbon::now()->year)
                ->when($filterPartnerId, $scopeAnimalRelation)
                ->sum('price');

            // Net Profit This Month
            // Optimized: Single Query Aggregation
            $netProfit = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->where('exit_type', 'SALE')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->whereYear('exit_date', Carbon::now()->year)
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->where('animals.partner_id', $filterPartnerId);
                })
                ->sum(DB::raw('price - (COALESCE(animals.purchase_price, 0) + exit_logs.final_hpp)'));

            // 4. Low Stock Alerts (Global Only)
            $lowStockItems = (!$filterPartnerId) ? InventoryItem::where('current_stock', '<', 10)->get() : [];

            // 5. Conception Rate
            $successfulBreeding = BreedingEvent::where('status', 'SUCCESS')
                 ->when($filterPartnerId, function($q) use ($filterPartnerId) {
                     $q->whereHas('dam', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                 })
                 ->count();
            $failedBreeding = BreedingEvent::where('status', 'FAILED')
                 ->when($filterPartnerId, function($q) use ($filterPartnerId) {
                     $q->whereHas('dam', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                 })
                 ->count();

            $totalCompleted = $successfulBreeding + $failedBreeding;
            $conceptionRate = $totalCompleted > 0 ? ($successfulBreeding / $totalCompleted) * 100 : 0;

            // 6. Full Performance Stats
            $feedUsage = 0;
            $medicineCost = 0;
            $totalFeedCost = 0;
            $totalMedicineCost = 0;
            
            if (!$filterPartnerId) {
                $feedLogs = InventoryUsageLog::whereHas('item', function($q) {
                        $q->where('category', 'FEED');
                    })
                    ->whereMonth('usage_date', Carbon::now()->month)
                    ->get();

                 // Pre-fetch average prices
                $avgPrices = DB::table('inventory_purchases')
                    ->select('item_id', DB::raw('SUM(price_total) / SUM(qty) as avg_price'))
                    ->groupBy('item_id')
                    ->pluck('avg_price', 'item_id');

                foreach ($feedLogs as $log) {
                    $price = $avgPrices[$log->item_id] ?? 0;
                    $totalFeedCost += ($log->qty_used + $log->qty_wasted) * $price;
                }
                $feedUsage = $feedLogs->sum('qty_used');


                $medicineLogs = InventoryUsageLog::whereHas('item', function($q) {
                        $q->whereIn('category', ['MEDICINE', 'VITAMIN', 'VACCINE']);
                    })
                    ->whereMonth('usage_date', Carbon::now()->month)
                    ->with('item')
                    ->get();

                foreach ($medicineLogs as $log) {
                    $avgPrice = $avgPrices[$log->item_id] ?? 0;
                    $medicineCost += ($log->qty_used + $log->qty_wasted) * $avgPrice;
                }
                $totalMedicineCost = $medicineCost;

                // Add Estimated Operational Cost for Global View too
                $estOpsCost = $activeAnimals * 15000;

                $expenseLabels = ['Pakan (Feed)', 'Obat & Vitamin', 'Operasional (Estimasi)'];
                $expenseData = [$totalFeedCost, $totalMedicineCost, $estOpsCost];
            } else {
                // ESTIMATION for Partner View
                $estFeedCost = $activeAnimals * 5000 * 30;
                $estHealthCost = $activeAnimals * 10000;
                $estOpsCost = $activeAnimals * 15000;

                $expenseLabels = ['Pakan (Estimasi)', 'Kesehatan (Estimasi)', 'Operasional (Estimasi)'];
                $expenseData = [$estFeedCost, $estHealthCost, $estOpsCost];
            }

            // Mortality Stats This Month
            $deathCount = ExitLog::where('exit_type', 'DEATH')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->when($filterPartnerId, $scopeAnimalRelation)
                ->count();

            // Lost Asset Value
            $deathValue = ExitLog::where('exit_type', 'DEATH')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->when($filterPartnerId, $scopeAnimalRelation)
                ->join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->sum(DB::raw('COALESCE(animals.purchase_price, 0) + exit_logs.final_hpp'));

            // 7. Breakdown by Sex (Live)
            $liveMale = Animal::where('is_active', true)->where('gender', 'MALE')->when($filterPartnerId, $scopePartner)->count();
            $liveFemale = Animal::where('is_active', true)->where('gender', 'FEMALE')->when($filterPartnerId, $scopePartner)->count();

            // 8. Breakdown by Sex (Dead - This Month)
            $deadMale = ExitLog::where('exit_type', 'DEATH')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->whereHas('animal', function($q) use ($filterPartnerId) { 
                    $q->where('gender', 'MALE');
                    if ($filterPartnerId) $q->where('partner_id', $filterPartnerId);
                })
                ->count();
            $deadFemale = ExitLog::where('exit_type', 'DEATH')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->whereHas('animal', function($q) use ($filterPartnerId) { 
                    $q->where('gender', 'FEMALE');
                    if ($filterPartnerId) $q->where('partner_id', $filterPartnerId);
                })
                ->count();

            // 9. Alerts
            // Optimization: Add Index to birth_date and next_due_date in migration if possible
            $separationCandidates = Animal::where('is_active', true)
                ->whereDate('birth_date', '<=', Carbon::now()->subDays(60))
                ->whereHas('physStatus', function($q) { $q->where('name', 'Cempe Lahir'); })
                ->when($filterPartnerId, $scopePartner)
                ->take(50)
                ->get();

            $matingSeparationCandidates = BreedingEvent::where('status', 'PENDING')
                 ->whereDate('mating_date', '<=', Carbon::now()->subDays(60))
                 ->with(['dam', 'sire'])
                 ->when($filterPartnerId, function($q) use ($filterPartnerId) {
                     $q->whereHas('dam', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                 })
                 ->take(50)
                 ->get();

            $vaccineAlerts = TreatmentLog::where('type', 'VACCINE')
                ->whereNotNull('next_due_date')
                ->whereBetween('next_due_date', [Carbon::now(), Carbon::now()->addDays(14)])
                ->when($filterPartnerId, $scopeAnimalRelation)
                ->with('animal')
                ->orderBy('next_due_date', 'asc')
                ->take(50)
                ->get();

            $weaningAlerts = Animal::where('current_phys_status_id', 1)
                 ->whereDate('birth_date', '<=', Carbon::now()->subDays(35))
                 ->when($filterPartnerId, $scopePartner)
                 ->with(['category', 'location'])
                 ->take(50)
                 ->get();

            // --- CHARTS DATA ---
            
            // A. Mortality Trend
            $mortalityTrendLabels = [];
            $mortalityTrendData = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthName = $date->format('M Y');
                $count = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                    ->where('exit_type', 'DEATH')
                    ->whereMonth('exit_date', $date->month)
                    ->whereYear('exit_date', $date->year)
                    ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                        $q->where('animals.partner_id', $filterPartnerId);
                    })
                    ->count();
                $mortalityTrendLabels[] = $monthName;
                $mortalityTrendData[] = $count;
            }

            // B. Financial Summary
            $financialLabels = [];
            $financialRevenue = [];
            $financialLoss = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $financialLabels[] = $date->format('M Y');

                $rev = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                    ->where('exit_type', 'SALE')
                    ->whereMonth('exit_date', $date->month)
                    ->whereYear('exit_date', $date->year)
                    ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                        $q->where('animals.partner_id', $filterPartnerId);
                    })
                    ->sum('price');
                $financialRevenue[] = $rev;

                $loss = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                    ->where('exit_type', 'DEATH')
                    ->whereMonth('exit_date', $date->month)
                    ->whereYear('exit_date', $date->year)
                    ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                        $q->where('animals.partner_id', $filterPartnerId);
                    })
                    ->sum(DB::raw('COALESCE(animals.purchase_price, 0) + exit_logs.final_hpp'));
                $financialLoss[] = $loss;
            }

            // C. Demographics
            $demographics = Animal::where('is_active', true)
                ->when($filterPartnerId, $scopePartner)
                ->join('master_phys_statuses', 'animals.current_phys_status_id', '=', 'master_phys_statuses.id')
                ->select('master_phys_statuses.name', DB::raw('count(*) as total'))
                ->groupBy('master_phys_statuses.name')
                ->pluck('total', 'name')
                ->toArray();
            $demographicsLabels = array_keys($demographics);
            $demographicsData = array_values($demographics);

            // Optimized Biomass Calculation (SQL Pushdown)
            // Replaces O(N*M) PHP loop with 6 optimized SQL queries (O(N) db-side)
            $biomassLabels = [];
            $biomassDataMale = [];
            $biomassDataFemale = [];
            $biomassDataKids = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i)->endOfMonth();
                $biomassLabels[] = $date->format('M Y');
                $dateStr = $date->format('Y-m-d');
                $oneYearAgo = $date->copy()->subYear()->format('Y-m-d');

                // Find the "Latest Weight" for each animal AS OF $dateStr
                $stats = DB::table('animals')
                    ->join('weight_logs', function ($join) use ($dateStr) {
                        $join->on('animals.id', '=', 'weight_logs.animal_id')
                             ->whereRaw("weight_logs.weigh_date = (
                                 SELECT MAX(wl2.weigh_date) 
                                 FROM weight_logs as wl2 
                                 WHERE wl2.animal_id = animals.id 
                                 AND wl2.weigh_date <= ?
                             )", [$dateStr]);
                    })
                    ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                        $q->where('animals.partner_id', $filterPartnerId);
                    })
                    ->selectRaw("
                        SUM(CASE WHEN animals.gender = 'MALE' AND animals.birth_date <= ? THEN weight_logs.weight_kg ELSE 0 END) as male_biomass,
                        SUM(CASE WHEN animals.gender = 'FEMALE' AND animals.birth_date <= ? THEN weight_logs.weight_kg ELSE 0 END) as female_biomass,
                        SUM(CASE WHEN animals.birth_date > ? THEN weight_logs.weight_kg ELSE 0 END) as kid_biomass
                    ", [$oneYearAgo, $oneYearAgo, $oneYearAgo])
                    ->first();

                $biomassDataMale[] = $stats->male_biomass ?? 0;
                $biomassDataFemale[] = $stats->female_biomass ?? 0;
                $biomassDataKids[] = $stats->kid_biomass ?? 0;
            }

            return compact(
                'filterPartnerId',
                'activeAnimals', 'populationByCage', 'avgAdg', 'totalStockValue',
                'salesThisMonth', 'netProfit', 'lowStockItems', 'conceptionRate',
                'feedUsage', 'medicineCost', 'deathCount', 'deathValue',
                'liveMale', 'liveFemale', 'deadMale', 'deadFemale',
                'separationCandidates', 'matingSeparationCandidates',
                'vaccineAlerts', 'weaningAlerts',
                // Chart Data
                'mortalityTrendLabels', 'mortalityTrendData',
                'financialLabels', 'financialRevenue', 'financialLoss',
                'demographicsLabels', 'demographicsData',
                'expenseLabels', 'expenseData',
                'biomassLabels', 'biomassDataMale', 'biomassDataFemale', 'biomassDataKids'
            );
        });
    }
}
