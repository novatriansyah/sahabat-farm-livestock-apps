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

        $logs = $animal->weightLogs()
            ->orderBy('weigh_date', 'asc')
            ->get()
            ->values();

        if ($logs->count() < 2) {
            $animal->update(['daily_adg' => 0]);
            return;
        }

        $lastLog = $logs->last();
        $prevLog = $logs->get($logs->count() - 2);

        $currentDate = Carbon::parse($lastLog->weigh_date);
        $prevDate = Carbon::parse($prevLog->weigh_date);

        $daysDiff = $prevDate->diffInDays($currentDate);

        if ($daysDiff > 0) {
            $weightDiff = $lastLog->weight_kg - $prevLog->weight_kg;
            $adg = $weightDiff / $daysDiff;
        } else {
            $adg = 0;
        }

        $animal->update(['daily_adg' => round($adg, 3)]);

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
