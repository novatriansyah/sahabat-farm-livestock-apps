<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\ExitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 1. Births (Animals born in this month)
        $birthsQuery = Animal::whereMonth('birth_date', $month)
            ->whereYear('birth_date', $year)
            ->with(['dam', 'sire', 'breed', 'weightLogs']);
        
        // 2. Deaths (Exit Logs type DEATH)
        $deathsQuery = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', $month)
            ->whereYear('exit_date', $year)
            ->with(['animal.breed', 'animal.partner']);

        if ($request->user()->role === 'PARTNER') {
            $partnerId = $request->user()->partner_id;
            $birthsQuery->where('partner_id', $partnerId);
            $deathsQuery->whereHas('animal', function($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            });
        }

        $births = $birthsQuery->get();
        $deaths = $deathsQuery->get();

        return view('admin.reports.index', compact('births', 'deaths', 'month', 'year'));
    }

    public function sales(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Sales (Exit Logs type SALE)
        $salesQuery = ExitLog::where('exit_type', 'SALE')
            ->whereMonth('exit_date', $month)
            ->whereYear('exit_date', $year)
            ->with(['animal.breed', 'animal.partner']);

        if ($request->user()->role === 'PARTNER') {
            $partnerId = $request->user()->partner_id;
            $salesQuery->whereHas('animal', function($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            });
        }

        $sales = $salesQuery->get();
        
        // Calculate Totals
        $totalRevenue = $sales->sum('price');
        $totalProfit = $sales->sum(function($sale) {
            return $sale->price - ($sale->final_hpp ?? 0);
        });

        return view('admin.reports.sales', compact('sales', 'totalRevenue', 'totalProfit', 'month', 'year'));
    }

    public function stock(Request $request): View
    {
        $baseQuery = Animal::where('is_active', true);
        
        // Partner Scoping
        if ($request->user()->role === 'PARTNER') {
            $baseQuery->where('partner_id', $request->user()->partner_id);
        }

        // Calculate Summaries
        $byGender = [
            'MALE' => (clone $baseQuery)->where('gender', 'MALE')->count(),
            'FEMALE' => (clone $baseQuery)->where('gender', 'FEMALE')->count(),
            'TOTAL' => (clone $baseQuery)->count(),
        ];

        $byBreed = (clone $baseQuery)->with('breed')->get()->groupBy(function($item) {
            return $item->breed->name ?? 'Unknown';
        })->map->count()->sortDesc();

        $byLocation = (clone $baseQuery)->with('location')->get()->groupBy(function($item) {
            return $item->location->name ?? 'No Location';
        })->map(function($group) {
            return [
                'male' => $group->where('gender', 'MALE')->count(),
                'female' => $group->where('gender', 'FEMALE')->count(),
                'total' => $group->count()
            ];
        })->sortByDesc('total');

        // Main List
        $query = clone $baseQuery;
        $query->with(['breed', 'location', 'partner']);

        if ($request->input('mode') === 'print') {
            $animals = $query->get();
        } else {
            $animals = $query->paginate(50);
        }

        return view('admin.reports.stock', compact('animals', 'byGender', 'byBreed', 'byLocation'));
    }

    public function partners(Request $request): View
    {
        // 1. Determine Target Partner
        $partners = \App\Models\MasterPartner::all();
        $targetPartnerId = null;

        if ($request->user()->role === 'PARTNER') {
            $targetPartnerId = $request->user()->partner_id;
        } elseif ($request->has('partner_id')) {
            $targetPartnerId = $request->input('partner_id');
        }

        // 2. Fetch Data if Partner Selected
        $summary = null;
        $activeAnimals = collect();
        $salesHistory = collect();

        if ($targetPartnerId) {
            // Active Assets
            $activeAnimals = Animal::where('partner_id', $targetPartnerId)
                ->where('is_active', true)
                ->with(['breed', 'location'])
                ->get();

            // Sales History
            $salesHistory = ExitLog::whereHas('animal', function($q) use ($targetPartnerId) {
                    $q->where('partner_id', $targetPartnerId);
                })
                ->where('exit_type', 'SALE')
                ->with(['animal.breed'])
                ->orderBy('exit_date', 'desc')
                ->get();

            // Calculate Metrics
            $totalInvested = $activeAnimals->sum('purchase_price'); // Asset Value = Purchase Price of active
            
            // Profit is Realized from Sales
            $totalProfitRealized = $salesHistory->sum(function($sale) {
                return $sale->price - ($sale->final_hpp ?? 0);
            });

            $summary = [
                'total_animals' => $activeAnimals->count(),
                'asset_value' => $totalInvested,
                'total_sales_revenue' => $salesHistory->sum('price'),
                'total_profit' => $totalProfitRealized
            ];
        }

        return view('admin.reports.partners', compact('partners', 'targetPartnerId', 'summary', 'activeAnimals', 'salesHistory'));
    }

    public function operational(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 1. Fetch Usage Logs for the Period
        $usages = \App\Models\InventoryUsageLog::whereMonth('usage_date', $month)
            ->whereYear('usage_date', $year)
            ->with(['item', 'location'])
            ->get();

        // 2. Calculate Costs (Simple Average Cost Method)
        // Ideally this should be FIFO, but for now we use Average Price from Purchases
        $usageSummary = [];
        $totalCost = 0;
        
        // Group by Item to calculate efficiency
        $groupedByItem = $usages->groupBy('item_id');
        
        foreach ($groupedByItem as $itemId => $logs) {
            $item = $logs->first()->item;
            $totalQty = $logs->sum('qty_used');
            
            // Get Average Price from Purchases
            $avgPrice = \App\Models\InventoryPurchase::where('item_id', $itemId)
                ->selectRaw('SUM(price_total) / SUM(qty) as avg_price')
                ->value('avg_price') ?? 0;

            $cost = $totalQty * $avgPrice;
            $totalCost += $cost;

            $usageSummary[] = [
                'item_name' => $item->name,
                'unit' => $item->unit,
                'qty_used' => $totalQty,
                'avg_price' => $avgPrice,
                'total_cost' => $cost
            ];
        }

        // 3. Group by Location
        $locationSummary = $usages->groupBy(function($log) {
            return $log->location->name ?? 'General Farm';
        })->map(function($group) {
            return $group->sum('qty_used'); // Just Qty for now, mixing units is tricky.
        });

        return view('admin.reports.operational', compact('month', 'year', 'usageSummary', 'totalCost', 'locationSummary'));
    }

    public function performance(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 1. Fetch Weight Logs for the Period
        // We need logs from this month to compare against previous records
        $currentLogs = \App\Models\WeightLog::whereMonth('weigh_date', $month)
            ->whereYear('weigh_date', $year)
            ->with(['animal.breed', 'animal.location'])
            ->get();

        $adgData = collect();
        
        foreach ($currentLogs as $log) {
            // Find the most recent weight BEFORE this log
            $prevLog = \App\Models\WeightLog::where('animal_id', $log->animal_id)
                ->where('weigh_date', '<', $log->weigh_date)
                ->orderBy('weigh_date', 'desc')
                ->first();

            if ($prevLog) {
                $days = Carbon::parse($prevLog->weigh_date)->diffInDays($log->weigh_date);
                
                if ($days > 0) {
                    $weightDiff = $log->weight_kg - $prevLog->weight_kg;
                    $adg = $weightDiff / $days; // kg per day
                    
                    // Convert to grams for easier reading
                    $adgGrams = $adg * 1000;

                    $adgData->push([
                        'animal' => $log->animal,
                        'current_weight' => $log->weight_kg,
                        'prev_weight' => $prevLog->weight_kg,
                        'days_interval' => $days,
                        'adg_grams' => $adgGrams,
                        'breed_name' => $log->animal->breed->name ?? 'Unknown',
                        'location_name' => $log->animal->location->name ?? 'Unknown'
                    ]);
                }
            }
        }

        // 2. Aggregate by Breed
        $breedStats = $adgData->groupBy('breed_name')->map(function($group) {
            return [
                'avg_adg' => $group->avg('adg_grams'),
                'count' => $group->count(),
                'max_adg' => $group->max('adg_grams'),
                'min_adg' => $group->min('adg_grams'),
            ];
        })->sortByDesc('avg_adg');

        // 3. Top Performers (Top 10)
        $topPerformers = $adgData->sortByDesc('adg_grams')->take(10);
        
        // 4. Low Performers (Bottom 5 - alerting for health issues)
        $lowPerformers = $adgData->sortBy('adg_grams')->take(5);

        return view('admin.reports.performance', compact('month', 'year', 'breedStats', 'topPerformers', 'lowPerformers'));
    }

    public function reproduction(Request $request): View
    {
        // 1. Fetch Productive Females (Dams)
        $dams = \App\Models\Animal::where('gender', 'FEMALE')
            ->whereHas('offspring') // Only those who have given birth
            ->with(['offspring' => function($q) {
                $q->orderBy('birth_date', 'asc');
            }, 'breed'])
            ->get();

        $reproData = collect();

        foreach ($dams as $dam) {
            $offspring = $dam->offspring;
            
            // Group by birth date to identify "Lambing Events" (Litters)
            $litters = $offspring->groupBy(function($item) {
                return $item->birth_date->format('Y-m-d');
            });

            $totalLitters = $litters->count();
            $totalOffspring = $offspring->count();
            $avgLitterSize = $totalLitters > 0 ? $totalOffspring / $totalLitters : 0;

            // Calculate Intervals
            $intervals = [];
            $dates = $litters->keys()->sort()->values(); // Sorted dates Y-m-d
            
            for ($i = 1; $i < $dates->count(); $i++) {
                $prev = Carbon::parse($dates[$i-1]);
                $curr = Carbon::parse($dates[$i]);
                $intervals[] = $prev->diffInDays($curr);
            }

            $avgInterval = count($intervals) > 0 ? array_sum($intervals) / count($intervals) : 0;
            $lastBirth = $dates->last() ? Carbon::parse($dates->last()) : null;

            $reproData->push([
                'dam' => $dam,
                'total_offspring' => $totalOffspring,
                'total_litters' => $totalLitters,
                'avg_litter_size' => $avgLitterSize,
                'avg_interval_days' => (int) $avgInterval,
                'last_birth_date' => $lastBirth,
                'days_since_last_birth' => $lastBirth ? (int) $lastBirth->diffInDays(Carbon::now()) : 0
            ]);
        }

        // Sort by Dams with most litters (proven breeders)
        $reproData = $reproData->sortByDesc('total_litters');

        return view('admin.reports.reproduction', compact('reproData'));
    }

    public function audit(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 1. Deaths by Location
        $deaths = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', $month)
            ->whereYear('exit_date', $year)
            ->with(['animal.location'])
            ->get()
            ->groupBy(function($log) {
                return $log->animal->location->name ?? 'Unknown';
            });

        // 2. Births by Location
        $births = Animal::whereMonth('birth_date', $month)
            ->whereYear('birth_date', $year)
            ->with(['location'])
            ->get()
            ->groupBy(function($animal) {
                return $animal->location->name ?? 'Unknown';
            });

        // 3. Current Population Statistics
        $population = Animal::where('is_active', true)
            ->with('location')
            ->get()
            ->groupBy(function($animal) {
                return $animal->location->name ?? 'Unknown';
            });

        // 4. Merge Data
        $locations = \App\Models\MasterLocation::pluck('name')->toArray();
        $auditData = [];

        foreach ($locations as $locName) {
            $deathCount = isset($deaths[$locName]) ? $deaths[$locName]->count() : 0;
            $birthCount = isset($births[$locName]) ? $births[$locName]->count() : 0;
            $popCount = isset($population[$locName]) ? $population[$locName]->count() : 0;
            
            // Avoid division by zero
            $mortalityRate = ($popCount + $deathCount) > 0 
                ? ($deathCount / ($popCount + $deathCount)) * 100 
                : 0;

            if ($deathCount > 0 || $birthCount > 0 || $popCount > 0) {
                $auditData[] = [
                    'location' => $locName,
                    'deaths' => $deathCount,
                    'births' => $birthCount,
                    'population' => $popCount,
                    'mortality_rate' => $mortalityRate
                ];
            }
        }
        
        // Sort by Mortality Rate (High to Low)
        usort($auditData, function($a, $b) {
            return $b['mortality_rate'] <=> $a['mortality_rate'];
        });

        return view('admin.reports.audit', compact('month', 'year', 'auditData'));
    }
}
