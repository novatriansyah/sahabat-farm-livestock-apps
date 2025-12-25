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
            // Get all usage logs for the date where category is FEED
            // Note: If 'category' is null, we assume FEED for backward compatibility, or strictly check 'FEED'.
            // Let's rely on item relationship.
            $usageLogs = InventoryUsageLog::whereDate('usage_date', $date)
                ->with('item')
                ->get()
                ->filter(function ($log) {
                    return $log->item->category === 'FEED';
                });

            if ($usageLogs->isEmpty()) {
                return;
            }

            // Cache item prices to avoid N+1 queries
            $itemPrices = [];

            // Group logs by Location (Cage)
            $logsByLocation = $usageLogs->groupBy('location_id');

            // General Cost (No Location)
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

                // FIX: Only count animals that were actually physically present on this date.
                // We use 'entry_date' (if available) or fall back to 'created_at'.
                // If the animal was born on farm, entry_date should be birth_date.
                $animalsInLocationQuery = Animal::where('is_active', true)
                    ->where('current_location_id', $locationId)
                    ->where(function($q) use ($date) {
                        $q->whereDate('entry_date', '<=', $date)
                          ->orWhere(function($sub) use ($date) {
                              $sub->whereNull('entry_date')
                                  ->whereDate('created_at', '<=', $date);
                          });
                    });

                $animalsInLocationCount = $animalsInLocationQuery->count();

                if ($animalsInLocationCount > 0) {
                    $costPerHead = $locationCost / $animalsInLocationCount;

                    // Update only those specific animals
                    $animalsInLocationQuery->increment('accumulated_feed_cost', $costPerHead);
                    $animalsInLocationQuery->increment('current_hpp', $costPerHead);
                }
            }

            // Process General Cost (Distributed to ALL active animals present on date)
            if ($generalCost > 0) {
                $totalActiveQuery = Animal::where('is_active', true)
                    ->where(function($q) use ($date) {
                        $q->whereDate('entry_date', '<=', $date)
                          ->orWhere(function($sub) use ($date) {
                              $sub->whereNull('entry_date')
                                  ->whereDate('created_at', '<=', $date);
                          });
                    });

                $totalActiveCount = $totalActiveQuery->count();

                if ($totalActiveCount > 0) {
                    $generalCostPerHead = $generalCost / $totalActiveCount;
                    $totalActiveQuery->increment('accumulated_feed_cost', $generalCostPerHead);
                    $totalActiveQuery->increment('current_hpp', $generalCostPerHead);
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
