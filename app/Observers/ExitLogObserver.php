<?php

namespace App\Observers;

use App\Models\ExitLog;
use Illuminate\Support\Facades\Cache;

class ExitLogObserver
{
    /**
     * Handle the ExitLog "created" event.
     */
    public function created(ExitLog $exitLog): void
    {
        $this->clearDashboardCache($exitLog);
    }

    /**
     * Handle the ExitLog "updated" event.
     */
    public function updated(ExitLog $exitLog): void
    {
        $this->clearDashboardCache($exitLog);
    }

    /**
     * Handle the ExitLog "deleted" event.
     */
    public function deleted(ExitLog $exitLog): void
    {
        $this->clearDashboardCache($exitLog);
    }

    private function clearDashboardCache(ExitLog $exitLog): void
    {
        // Clear Global Cache
        Cache::forget('dashboard_stats_global');

        // Clear Partner Cache if exists (via Animal)
        if ($exitLog->animal && $exitLog->animal->partner_id) {
            Cache::forget('dashboard_stats_' . $exitLog->animal->partner_id);
        }
    }
}
