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
            return ['eligible' => false, 'reason' => 'Data Ras/Bangsa tidak ditemukan.'];
        }

        // Rule 1: Age
        $ageMonths = $animal->birth_date->diffInMonths(Carbon::now());
        $minAge = $breed->min_age_mate_months ?? (int) \App\Models\FarmSetting::get('min_age_mate_months_fallback', 8);

        if ($ageMonths < $minAge) {
            return [
                'eligible' => false,
                'reason' => "Usia terlalu muda ({$ageMonths} bulan). Minimal adalah {$minAge} bulan."
            ];
        }

        // Rule 2: Weight (Latest Log)
        $latestWeight = $animal->weightLogs()->orderByDesc('weigh_date')->first();
        if (!$latestWeight) {
            return ['eligible' => false, 'reason' => 'Data timbangan belum ditemukan. Harap timbang terlebih dahulu.'];
        }

        $minWeight = $breed->min_weight_mate ?? (float) \App\Models\FarmSetting::get('min_weight_mate_fallback', 30);
        if ($latestWeight->weight_kg < $minWeight) {
            return [
                'eligible' => false,
                'reason' => "Berat badan kurang ({$latestWeight->weight_kg} kg). Minimal adalah {$minWeight} kg."
            ];
        }

        // Rule 3: Post-Birth Recovery (Nifas)
        $nifasPeriod = (int) \App\Models\FarmSetting::get('nifas_period_days', 40);
        $lastBirth = Animal::where('dam_id', $animal->id)
            ->orderByDesc('birth_date')
            ->first();

        if ($lastBirth) {
            $daysSinceBirth = $lastBirth->birth_date->diffInDays(Carbon::now());
            if ($daysSinceBirth < $nifasPeriod) {
                return [
                    'eligible' => false,
                    'reason' => "Indukan dalam masa pemulihan (nifas). Minimal {$nifasPeriod} hari (baru {$daysSinceBirth} hari)."
                ];
            }
        }

        // Rule 4: Health Status & Pregnancy
        // Using new dynamic flags instead of hardcoded IDs
        $status = $animal->physStatus;
        if (!$status->is_breedable || $status->is_pregnant || $status->is_quarantine) {
            return [
                'eligible' => false,
                'reason' => "Status Hewan tidak memungkinkan: " . ($status->name ?? 'Unknown')
            ];
        }

        return ['eligible' => true];
    }
}
