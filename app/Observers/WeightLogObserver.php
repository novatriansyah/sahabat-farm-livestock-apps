<?php

namespace App\Observers;

use App\Models\WeightLog;
use Carbon\Carbon;

class WeightLogObserver
{
    /**
     * Handle the WeightLog "created" event.
     */
    public function created(WeightLog $weightLog): void
    {
        $this->recalculateAdg($weightLog);
    }

    /**
     * Handle the WeightLog "updated" event.
     */
    public function updated(WeightLog $weightLog): void
    {
        $this->recalculateAdg($weightLog);
    }

    /**
     * Handle the WeightLog "deleted" event.
     */
    public function deleted(WeightLog $weightLog): void
    {
        // On delete, we might want to recalculate based on the *new* last log
        // For now, let's keep it simple or fetch the latest one.
        $latestLog = $weightLog->animal->weightLogs()->orderBy('weigh_date', 'desc')->first();
        if ($latestLog) {
            $this->recalculateAdg($latestLog);
        } else {
            // No logs left? Reset ADG to 0
            $weightLog->animal->update(['daily_adg' => 0]);
        }
    }

    protected function recalculateAdg(WeightLog $currentLog)
    {
        $animal = $currentLog->animal;

        // 1. Get all logs ordered by date
        // We need context to find the "Previous" log relative to this one (or the absolute latest if we just want current status)
        // Requirement: "The ADG for Feb 1 must only calculate the gain/loss between Jan 1 and Feb 1"
        // This implies the 'daily_adg' field on Animal reflects the *latest* trend.

        $logs = $animal->weightLogs()
            ->orderBy('weigh_date', 'asc')
            ->get();

        // If less than 2 logs, ADG is 0 (Initial weight only)
        if ($logs->count() < 2) {
            $animal->update(['daily_adg' => 0]);
            return;
        }

        // Get the very last log (Current Grid)
        $lastLog = $logs->last();
        
        // Get the second to last log (Previous)
        // Note: logs is a Collection, keys are 0-indexed.
        $prevLog = $logs->get($logs->count() - 2);

        // Validation: Ensure valid dates
        $currentDate = Carbon::parse($lastLog->weigh_date);
        $prevDate = Carbon::parse($prevLog->weigh_date);

        // Calculate Days Difference
        $daysDiff = $prevDate->diffInDays($currentDate);

        if ($daysDiff > 0) {
            $weightDiff = $lastLog->weight_kg - $prevLog->weight_kg;
            $adg = $weightDiff / $daysDiff; // kg/day
        } else {
            // Avoid division by zero (e.g., two logs same day?)
            $adg = 0;
        }


        // Update Animal
        // Round to 3 decimal places (e.g., 0.150 kg)
        $animal->update(['daily_adg' => round($adg, 3)]);

        // Cache Invalidation
        $this->clearDashboardCache($animal);
    }

    private function clearDashboardCache($animal): void
    {
        // Clear Global Cache
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats_global');

        // Clear Partner Cache if exists
        if ($animal->partner_id) {
            \Illuminate\Support\Facades\Cache::forget('dashboard_stats_' . $animal->partner_id);
        }
    }
}
