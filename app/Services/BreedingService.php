<?php

namespace App\Services;

use App\Models\Animal;
use Carbon\Carbon;

class BreedingService
{
    /**
     * Check if an animal is eligible for mating based on Breed rules.
     */
    public function isEligibleForMating(Animal $animal): array
    {
        // 1. Check Sex (Must be Female for Dam checks, but let's assume caller handles context)
        // Generally, we check eligibility for the Dam mainly.

        $breed = $animal->breed;
        if (!$breed) {
            return ['eligible' => false, 'reason' => 'Breed unknown'];
        }

        // Rule 1: Age
        $ageMonths = $animal->birth_date->diffInMonths(Carbon::now());
        $minAge = $breed->min_age_mate_months ?? 8; // Default 8 months

        if ($ageMonths < $minAge) {
            return [
                'eligible' => false,
                'reason' => "Too young ({$ageMonths} months). Minimum is {$minAge} months."
            ];
        }

        // Rule 2: Weight (Latest Log)
        $latestWeight = $animal->weightLogs()->orderByDesc('weigh_date')->first();
        if (!$latestWeight) {
            return ['eligible' => false, 'reason' => 'No weight record found.'];
        }

        $minWeight = $breed->min_weight_mate ?? 30; // Default 30kg
        if ($latestWeight->weight_kg < $minWeight) {
            return [
                'eligible' => false,
                'reason' => "Underweight ({$latestWeight->weight_kg} kg). Minimum is {$minWeight} kg."
            ];
        }

        return ['eligible' => true];
    }
}
