<?php

namespace App\Actions\Finance;

use App\Models\Animal;
use App\Models\InventoryUsageLog;
use App\Models\InventoryPurchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalculateDailyHpp
{
    public function execute(?Carbon $date = null): void
    {
        $date = $date ?? Carbon::yesterday();

        DB::transaction(function () use ($date) {
            // Get all usage logs for the date
            $usageLogs = InventoryUsageLog::whereDate('usage_date', $date)->get();

            if ($usageLogs->isEmpty()) {
                return;
            }

            // Cache item prices to avoid N+1 queries
            $itemPrices = [];

            // Group logs by Location (Cage)
            $logsByLocation = $usageLogs->groupBy('location_id');

            // We also need to handle logs with NULL location (General usage).
            // For MVP, if location is NULL, we might distribute to ALL cages or just ignore.
            // Let's assume General Usage is distributed equally to all active animals.
            $generalCost = 0;
            if (isset($logsByLocation[''])) {
                 foreach ($logsByLocation[''] as $log) {
                    $price = $this->getItemPrice($log->item_id, $itemPrices);
                    $generalCost += ($log->qty_used + $log->qty_wasted) * $price;
                 }
                 unset($logsByLocation['']);
            }

            // Process Location-Specific Costs
            foreach ($logsByLocation as $locationId => $logs) {
                $locationCost = 0;
                foreach ($logs as $log) {
                    $price = $this->getItemPrice($log->item_id, $itemPrices);
                    $locationCost += ($log->qty_used + $log->qty_wasted) * $price;
                }

                $animalsInLocation = Animal::where('is_active', true)
                    ->where('current_location_id', $locationId)
                    ->count();

                if ($animalsInLocation > 0) {
                    $costPerHead = $locationCost / $animalsInLocation;
                    Animal::where('is_active', true)
                        ->where('current_location_id', $locationId)
                        ->increment('current_hpp', $costPerHead);
                }
            }

            // Process General Cost (Distributed to ALL active animals)
            if ($generalCost > 0) {
                $totalActive = Animal::where('is_active', true)->count();
                if ($totalActive > 0) {
                    $generalCostPerHead = $generalCost / $totalActive;
                    Animal::where('is_active', true)->increment('current_hpp', $generalCostPerHead);
                }
            }
        });
    }

    private function getItemPrice($itemId, array &$cache): float
    {
        if (isset($cache[$itemId])) {
            return $cache[$itemId];
        }

        $totalValue = InventoryPurchase::where('item_id', $itemId)->sum('price_total');
        $totalQty = InventoryPurchase::where('item_id', $itemId)->sum('qty');

        $price = ($totalQty > 0) ? ($totalValue / $totalQty) : 0;
        $cache[$itemId] = $price;

        return $price;
    }
}
