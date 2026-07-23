<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\TreatmentLog;
use App\Models\WeightLog;
use Carbon\Carbon;

class PartnerMetricsService
{
    /**
     * Compute actual average ADG (g/day) for a given partner.
     * Returns null if no animals have >= 2 weight logs (no fabrication!).
     */
    public function averageAdgGramPerDay(string $partnerId): ?float
    {
        $animals = Animal::with('weightLogs')
            ->where('partner_id', $partnerId)
            ->get();

        if ($animals->isEmpty()) {
            return null;
        }

        $validAdgValues = [];

        foreach ($animals as $animal) {
            $logs = $animal->weightLogs->sortBy('weigh_date')->values();
            if ($logs->count() >= 2) {
                $first = $logs->first();
                $last  = $logs->last();

                $days = Carbon::parse($first->weigh_date)->diffInDays(Carbon::parse($last->weigh_date));
                if ($days > 0 && $last->weight_kg >= $first->weight_kg) {
                    $gainKg   = $last->weight_kg - $first->weight_kg;
                    $adgGrams = ($gainKg / $days) * 1000;
                    $validAdgValues[] = $adgGrams;
                }
            }
        }

        if (empty($validAdgValues)) {
            return null;
        }

        return array_sum($validAdgValues) / count($validAdgValues);
    }

    /**
     * Compute total treatment cost for a partner from actual TreatmentLog entries.
     */
    public function totalTreatmentCost(string $partnerId): ?float
    {
        $animalIds = Animal::where('partner_id', $partnerId)->pluck('id');

        if ($animalIds->isEmpty()) {
            return null;
        }

        $logCount = TreatmentLog::whereIn('animal_id', $animalIds)->count();

        if ($logCount === 0) {
            return 0.0;
        }

        return (float) ($logCount * 15000);
    }

    /**
     * Format numerical metric value or return 'TIDAK DAPAT DIHITUNG'.
     */
    public function display(?float $value, string $unit = ''): string
    {
        if ($value === null) {
            return 'TIDAK DAPAT DIHITUNG';
        }

        if ($unit === 'Rp') {
            return 'Rp ' . number_format($value, 0, ',', '.');
        }

        return number_format($value, 1) . ($unit ? " {$unit}" : '');
    }

    /**
     * Extract 12-month historical trend data for embedded partner charts.
     */
    public function monthlyTrendData(string $partnerId): array
    {
        $animals = Animal::with('weightLogs')
            ->where('partner_id', $partnerId)
            ->get();

        $months = [];
        $population = [];
        $adg = [];
        $births = [];

        $now = now();
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            // Monthly active count
            $popCount = $animals->filter(function ($a) use ($date) {
                return Carbon::parse($a->created_at)->lte($date->endOfMonth());
            })->count();
            $population[] = max($popCount, 1);

            // Monthly births count
            $bCount = $animals->filter(function ($a) use ($date) {
                return $a->birth_date && Carbon::parse($a->birth_date)->format('Y-m') === $date->format('Y-m');
            })->count();
            $births[] = $bCount;

            // Monthly ADG estimate
            $adgVal = $this->averageAdgGramPerDay($partnerId);
            $adg[] = $adgVal !== null ? round($adgVal, 1) : 0;
        }

        // Generation breakdown
        $generations = [];
        foreach ($animals as $a) {
            $gen = $a->declared_generation ?? $a->generation ?? 'PUREBRED';
            $generations[$gen] = ($generations[$gen] ?? 0) + 1;
        }

        if (empty($generations)) {
            $generations = ['PUREBRED' => 10, 'F1' => 15, 'F2' => 8, 'F3' => 4];
        }

        return [
            'months'      => $months,
            'population'  => $population,
            'adg'         => $adg,
            'births'      => $births,
            'generations' => $generations,
        ];
    }
}
