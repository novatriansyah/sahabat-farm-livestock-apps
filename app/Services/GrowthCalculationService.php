<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\WeightLog;

class GrowthCalculationService
{
    /**
     * Calculate ADG and performance metrics for an animal.
     */
    public function calculateForAnimal(Animal $animal): array
    {
        $logs = $animal->weightLogs()
            ->where('is_current', true)
            ->orderBy('weigh_date', 'asc')
            ->get();

        if ($logs->count() < 2) {
            $animal->update(['daily_adg' => null]);
            return [
                'adg' => null,
                'adg_g_day' => null,
                'status' => 'NOT_CALCULABLE',
                'display' => 'TIDAK DAPAT DIHITUNG',
                'badge' => 'UNKNOWN',
                'log_count' => $logs->count(),
            ];
        }

        $first = $logs->first();
        $latest = $logs->last();

        $days = $first->weigh_date->diffInDays($latest->weigh_date);
        if ($days <= 0) {
            $animal->update(['daily_adg' => null]);
            return [
                'adg' => null,
                'adg_g_day' => null,
                'status' => 'NOT_CALCULABLE',
                'display' => 'TIDAK DAPAT DIHITUNG',
                'badge' => 'UNKNOWN',
                'log_count' => $logs->count(),
            ];
        }

        $weightDiff = $latest->weight_kg - $first->weight_kg;
        $adgKgDay = $weightDiff / $days;
        $adgGDay = round($adgKgDay * 1000);

        // Determine if calculation is ACTUAL or PROVISIONAL
        $hasAssumedOrEstimated = $logs->contains(function (WeightLog $log) {
            return in_array(strtoupper((string) $log->measurement_status), ['ASSUMED', 'ESTIMATED']);
        });

        $status = $hasAssumedOrEstimated ? 'PROVISIONAL' : 'ACTUAL';
        $badge = $hasAssumedOrEstimated ? 'PROVISIONAL' : 'ACTUAL';

        $animal->update(['daily_adg' => $adgKgDay]);

        return [
            'adg' => $adgKgDay,
            'adg_g_day' => $adgGDay,
            'status' => $status,
            'display' => number_format($adgGDay, 0) . ' g/hari (' . $status . ')',
            'badge' => $badge,
            'log_count' => $logs->count(),
        ];
    }
}
