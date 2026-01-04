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
    public function index(Request $request, \App\Services\DashboardService $dashboardService)
    {
        $filterPartnerId = $request->input('partner_id');
        $partners = \App\Models\MasterPartner::all();

        // Delegate logic to Service
        $data = $dashboardService->getDashboardData($filterPartnerId);
        
        // Extract for easier usage in AJAX block below
        extract($data);

        if ($request->ajax()) {
            // Return ONLY what the JS needs to avoid circular reference issues with Eloquent Models
            // OPTIMIZED JSON PAYLOAD (Type Casting)
            $ajaxData = [
                'activeAnimals' => (int) $activeAnimals,
                'avgAdg' => round((float) $avgAdg, 3),
                'salesThisMonth' => (float) $salesThisMonth,
                'netProfit' => (float) $netProfit,
                'feedUsage' => round((float) $feedUsage, 1),
                'medicineCost' => (float) $medicineCost,
                'deathCount' => (int) $deathCount,
                'deathValue' => (float) $deathValue,
                'liveMale' => (int) $liveMale,
                'liveFemale' => (int) $liveFemale,
                'deadMale' => (int) $deadMale,
                'deadFemale' => (int) $deadFemale,
                
                // Chart Data (Ensure numeric arrays)
                'demographicsLabels' => $demographicsLabels,
                'demographicsData' => array_map('intval', $demographicsData),
                'financialLabels' => $financialLabels,
                'financialRevenue' => array_map('floatval', $financialRevenue),
                'financialLoss' => array_map('floatval', $financialLoss),
                'mortalityTrendLabels' => $mortalityTrendLabels,
                'mortalityTrendData' => array_map('intval', $mortalityTrendData),
                'expenseLabels' => $expenseLabels,
                'expenseData' => array_map('floatval', $expenseData),
                'biomassLabels' => $biomassLabels,
                'biomassDataMale' => array_map('floatval', $biomassDataMale),
                'biomassDataFemale' => array_map('floatval', $biomassDataFemale),
                'biomassDataKids' => array_map('floatval', $biomassDataKids),

                // Dynamic Alerts (formatted for JS)
                'vaccineAlerts' => $vaccineAlerts->map(function($log) {
                    return [
                        'tag_id' => $log->animal->tag_id,
                        'notes' => $log->notes,
                        'date' => $log->next_due_date->format('d M')
                    ];
                }),
                'weaningAlerts' => $weaningAlerts->map(function($animal) {
                    return [
                        'tag_id' => $animal->tag_id,
                        'age_days' => number_format($animal->birth_date->diffInDays(now()), 0),
                        'location' => $animal->location->name ?? '-'
                    ];
                }),
                'separationCandidates' => $separationCandidates->map(function($animal) {
                    return [
                        'tag_id' => $animal->tag_id,
                        'age_months' => number_format($animal->birth_date->floatDiffInMonths(now()), 1)
                    ];
                }),
                'matingSeparationCandidates' => $matingSeparationCandidates->map(function($event) {
                    return [
                        'dam_tag' => $event->dam->tag_id,
                        'sire_tag' => $event->sire->tag_id,
                        'date' => $event->mating_date->format('d M Y')
                    ];
                }),
                'lowStockItems' => collect($lowStockItems)->map(function($item) {
                     return [
                        'name' => $item->name,
                        'stock' => $item->current_stock,
                        'unit' => $item->unit
                     ];
                }),
            ];

            return response()->json($ajaxData);
        }

        return view('dashboard', array_merge($data, ['partners' => $partners]));
    }

}
