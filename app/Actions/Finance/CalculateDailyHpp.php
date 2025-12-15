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
            // 1. Calculate total feed cost for the target date
            // We need to determine the price per unit of the used feed.
            // This is a complex FIFO/Average Cost problem.
            // For MVP, we will use the "Latest Purchase Price" strategy or Average Price.
            // Let's go with Average Price from all purchases to keep it simple but functional.

            $usageLogs = InventoryUsageLog::whereDate('usage_date', $date)->get();

            $totalDailyCost = 0;

            foreach ($usageLogs as $log) {
                // Get average price per unit for this item
                $totalPurchaseValue = InventoryPurchase::where('item_id', $log->item_id)->sum('price_total');
                $totalPurchaseQty = InventoryPurchase::where('item_id', $log->item_id)->sum('qty');

                if ($totalPurchaseQty > 0) {
                    $pricePerUnit = $totalPurchaseValue / $totalPurchaseQty;
                    $cost = ($log->qty_used + $log->qty_wasted) * $pricePerUnit;
                    $totalDailyCost += $cost;
                }
            }

            if ($totalDailyCost <= 0) {
                return;
            }

            // 2. Count active animals
            $activeAnimalsCount = Animal::where('is_active', true)->count();

            if ($activeAnimalsCount === 0) {
                return;
            }

            // 3. Formula
            $dailyCostPerHead = $totalDailyCost / $activeAnimalsCount;

            // 4. Bulk Update
            Animal::where('is_active', true)->increment('current_hpp', $dailyCostPerHead);
        });
    }
}
