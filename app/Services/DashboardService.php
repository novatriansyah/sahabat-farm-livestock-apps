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

            // 2. Growth Performance (Avg ADG) & Avg HPP
            $avgAdg = Animal::where('is_active', true)
                 ->when($filterPartnerId, $scopePartner)
                 ->avg('daily_adg') ?? 0;

            $avgHpp = Animal::where('is_active', true)
                 ->when($filterPartnerId, $scopePartner)
                 ->avg('current_hpp') ?? 0;

            // 3. Financials
            $totalStockValue = 0;
            if (!$filterPartnerId) {
                 $totalStockValue = InventoryItem::sum(DB::raw('current_stock * (SELECT AVG(price_total/qty) FROM inventory_purchases WHERE item_id = inventory_items.id)'));
            }

            // Sales This Month
            $salesThisMonth = ExitLog::where('exit_type', 'JUAL')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->whereYear('exit_date', Carbon::now()->year)
                ->when($filterPartnerId, $scopeAnimalRelation)
                ->sum('price');

            // Net Profit This Month
            // Optimized: Single Query Aggregation
            $netProfit = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->where('exit_type', 'JUAL')
                ->whereMonth('exit_date', Carbon::now()->month)
                ->whereYear('exit_date', Carbon::now()->year)
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->where('animals.partner_id', $filterPartnerId);
                })
                ->sum(DB::raw('price - (COALESCE(animals.purchase_price, 0) + exit_logs.final_hpp)'));

            // 4. Low Stock Alerts (Global Only)
            $lowThreshold = (int) \App\Models\FarmSetting::get('low_stock_threshold', 10);
            $lowStockItems = (!$filterPartnerId) ? InventoryItem::where('current_stock', '<', $lowThreshold)->get() : [];

            // 5. Conception Rate
            $successfulBreeding = BreedingEvent::where('status', 'BERHASIL')
                 ->when($filterPartnerId, function($q) use ($filterPartnerId) {
                     $q->whereHas('dam', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                 })
                 ->count();
            $failedBreeding = BreedingEvent::where('status', 'GAGAL')
                 ->when($filterPartnerId, function($q) use ($filterPartnerId) {
                     $q->whereHas('dam', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                 })
                 ->count();

            $totalCompleted = $successfulBreeding + $failedBreeding;
            $conceptionRate = $totalCompleted > 0 ? ($successfulBreeding / $totalCompleted) * 100 : 0;

            // 6. Full Performance Stats (HYBRID FALLBACK LOGIC)
            $allActiveAnimals = Animal::where('is_active', true)->count();
            
            // A. Calculate Global Real Costs
            $feedUsage = 0;
            $totalFeedCost = 0;
            $medicineCost = 0;
            $totalMedicineCost = 0;
            $manualCosts = \App\Models\HppManualCost::where('month', Carbon::now()->format('Y-m'))->get();

            // Feed & Medicine Logs
            $feedLogs = InventoryUsageLog::whereHas('item', fn($q) => $q->where('category', 'Pakan'))
                ->whereMonth('usage_date', Carbon::now()->month)
                ->get();
            $medicineLogs = InventoryUsageLog::whereHas('item', fn($q) => $q->whereIn('category', ['Obat-Obatan', 'Vitamin', 'Vaksin']))
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
            foreach ($medicineLogs as $log) {
                $price = $avgPrices[$log->item_id] ?? 0;
                $totalMedicineCost += ($log->qty_used + $log->qty_wasted) * $price;
            }

            $totalRealExpenses = $totalFeedCost + $totalMedicineCost + $manualCosts->sum('amount');
            $isFallback = ($totalRealExpenses == 0);

            if ($isFallback) {
                // FALLBACK TO ESTIMATIONS
                $targetCount = $filterPartnerId ? $activeAnimals : $allActiveAnimals;
                $estFeed = (float) \App\Models\FarmSetting::get('est_feed_cost_day', 5000);
                $estHealth = (float) \App\Models\FarmSetting::get('est_health_cost_month', 10000);
                $estOps = (float) \App\Models\FarmSetting::get('est_ops_cost_month', 15000);

                $estFeedCost = $targetCount * $estFeed * 30; // ~30 days projection
                $estHealthCost = $targetCount * $estHealth;
                $estOpsCost = $targetCount * $estOps;

                $expenseLabels = ['Pakan (Estimasi)', 'Kesehatan (Estimasi)', 'Operasional (Estimasi)'];
                $expenseData = [$estFeedCost, $estHealthCost, $estOpsCost];
                
                $feedUsage = 0; // Usage cannot be estimated in kg easily
                $medicineCost = $estHealthCost; 
            } else {
                // USE REAL DATA (Prorated for Partners)
                $share = ($filterPartnerId && $allActiveAnimals > 0) ? ($activeAnimals / $allActiveAnimals) : 1;
                
                $expenseLabels = ['Pakan (Real)', 'Obat/Vaksin (Real)'];
                $expenseData = [$totalFeedCost * $share, $totalMedicineCost * $share];

                foreach($manualCosts as $mc) {
                    $expenseLabels[] = $mc->name . ' (Real)';
                    $expenseData[] = (float) $mc->amount * $share;
                }
                
                $feedUsage = $feedLogs->sum('qty_used') * $share;
                $medicineCost = $totalMedicineCost * $share;
            }

            // Mortality Stats All-Time
            $deathCount = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->where('exit_type', 'MATI')
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->where('animals.partner_id', $filterPartnerId);
                })
                ->count();

            // Lost Asset Value (All-Time)
            $deathValue = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->where('exit_type', 'MATI')
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->where('animals.partner_id', $filterPartnerId);
                })
                ->sum(DB::raw('COALESCE(animals.purchase_price, 0) + exit_logs.final_hpp'));

            // 7. Breakdown by Sex (Live)
            $liveMale = Animal::where('is_active', true)->where('gender', 'JANTAN')->when($filterPartnerId, $scopePartner)->count();
            $liveFemale = Animal::where('is_active', true)->where('gender', 'BETINA')->when($filterPartnerId, $scopePartner)->count();

            // 8. Breakdown by Sex (Dead - All-Time)
            $deadMale = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->where('exit_type', 'MATI')
                ->where('animals.gender', 'JANTAN')
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->where('animals.partner_id', $filterPartnerId);
                })
                ->count();

            $deadFemale = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                ->where('exit_type', 'MATI')
                ->where('animals.gender', 'BETINA')
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->where('animals.partner_id', $filterPartnerId);
                })
                ->count();

            // 9. Alerts
            $sepDays = (int) \App\Models\FarmSetting::get('separation_age_days', 60);
            $matSepDays = (int) \App\Models\FarmSetting::get('pregnancy_check_days', 60);
            $weanDays = (int) \App\Models\FarmSetting::get('weaning_age_days', 35);

            $separationCandidates = Animal::where('is_active', true)
                ->whereDate('birth_date', '<=', Carbon::now()->subDays($sepDays))
                ->whereHas('physStatus', function($q) { $q->where('name', 'like', '%Cempe%'); })
                ->when($filterPartnerId, $scopePartner)
                ->take(50)
                ->get();

            $matingSeparationCandidates = BreedingEvent::where('status', 'MENUNGGU')
                 ->whereDate('mating_date', '<=', Carbon::now()->subDays($matSepDays))
                 ->with(['dam', 'sire'])
                 ->when($filterPartnerId, function($q) use ($filterPartnerId) {
                     $q->whereHas('dam', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                 })
                 ->take(50)
                 ->get();

            $vaxDays = (int) \App\Models\FarmSetting::get('vaccine_alert_days', 14);
            $vaccineAlerts = TreatmentLog::where('type', 'VACCINE')
                ->whereNotNull('next_due_date')
                ->whereBetween('next_due_date', [Carbon::now(), Carbon::now()->addDays($vaxDays)])
                ->when($filterPartnerId, $scopeAnimalRelation)
                ->with('animal')
                ->orderBy('next_due_date', 'asc')
                ->take(50)
                ->get();

            $weaningAlerts = Animal::whereHas('physStatus', function($q) { $q->where('is_lactating', false)->where('name', 'like', '%Cempe%'); })
                 ->whereDate('birth_date', '<=', Carbon::now()->subDays($weanDays))
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
                    ->where('exit_type', 'MATI')
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
                    ->where('exit_type', 'JUAL')
                    ->whereMonth('exit_date', $date->month)
                    ->whereYear('exit_date', $date->year)
                    ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                        $q->where('animals.partner_id', $filterPartnerId);
                    })
                    ->sum('price');
                $financialRevenue[] = $rev;

                $loss = ExitLog::join('animals', 'exit_logs.animal_id', '=', 'animals.id')
                    ->where('exit_type', 'MATI')
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
                        SUM(CASE WHEN animals.gender = 'JANTAN' AND animals.birth_date <= ? THEN weight_logs.weight_kg ELSE 0 END) as male_biomass,
                        SUM(CASE WHEN animals.gender = 'BETINA' AND animals.birth_date <= ? THEN weight_logs.weight_kg ELSE 0 END) as female_biomass,
                        SUM(CASE WHEN animals.birth_date > ? THEN weight_logs.weight_kg ELSE 0 END) as kid_biomass
                    ", [$oneYearAgo, $oneYearAgo, $oneYearAgo])
                    ->first();

                $biomassDataMale[] = $stats->male_biomass ?? 0;
                $biomassDataFemale[] = $stats->female_biomass ?? 0;
                $biomassDataKids[] = $stats->kid_biomass ?? 0;
            }

            $pendingTasks = \App\Models\AnimalTask::where('status', 'PENDING')
                ->whereDate('due_date', '<=', Carbon::now())
                ->when($filterPartnerId, function ($q) use ($filterPartnerId) {
                    $q->whereHas('animal', fn($sq) => $sq->where('partner_id', $filterPartnerId));
                })
                ->with(['animal'])
                ->orderBy('due_date', 'asc')
                ->get();

            return compact(
                'filterPartnerId',
                'activeAnimals', 'populationByCage', 'avgAdg', 'avgHpp', 'totalStockValue',
                'salesThisMonth', 'netProfit', 'lowStockItems', 'conceptionRate',
                'feedUsage', 'medicineCost', 'deathCount', 'deathValue',
                'liveMale', 'liveFemale', 'deadMale', 'deadFemale',
                'separationCandidates', 'matingSeparationCandidates',
                'vaccineAlerts', 'weaningAlerts', 'pendingTasks',
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
