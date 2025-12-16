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

        // Rule 3: Post-Birth Recovery (Nifas) - 40 Days
        // Check last birth date (We need a way to track this. For now, check last BreedingEvent where status=SUCCESS/BIRTH?)
        // Or check if we have a 'last_birth_date' column. We don't.
        // But we can check if there are any offspring (animals where dam_id = this animal) born < 40 days ago.

        $lastBirth = Animal::where('dam_id', $animal->id)
            ->orderByDesc('birth_date')
            ->first();

        if ($lastBirth) {
            $daysSinceBirth = $lastBirth->birth_date->diffInDays(Carbon::now());
            if ($daysSinceBirth < 40) {
                return [
                    'eligible' => false,
                    'reason' => "Indukan dalam masa pemulihan (nifas). Baru {$daysSinceBirth} hari sejak melahirkan."
                ];
            }
        }

        return ['eligible' => true];
    }
}
