<?php

namespace App\Actions\Animal;

use App\Models\Animal;

class CalculateAdg
{
    public function execute(Animal $animal): void
    {
        // 1. Fetch the latest 2 weight logs
        $logs = $animal->weightLogs()
            ->orderByDesc('weigh_date')
            ->take(2)
            ->get();

        if ($logs->count() < 2) {
            return;
        }

        $current = $logs->first();
        $previous = $logs->last();

        // 2. Calculate interval
        // Ensure dates are Carbon instances (casted in model)
        $days = $previous->weigh_date->diffInDays($current->weigh_date);

        if ($days <= 0) {
            return;
        }

        // 3. Formula: (CurrentWeight - PreviousWeight) / DaysInterval
        $gain = $current->weight_kg - $previous->weight_kg;
        $adg = $gain / $days;

        // 4. Update animal
        $animal->update(['daily_adg' => $adg]);
    }
}
