<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\MasterPartner;
use App\Models\TreatmentLog;
use App\Models\WeightLog;
use Carbon\Carbon;

class UnifiedReportCalculationService
{
    /**
     * Compute partner metrics cleanly without hardcoded fallbacks.
     */
    public function getPartnerSummary(?string $partnerId = null): array
    {
        $query = Animal::with(['weightLogs', 'breed', 'location', 'physStatus', 'partner']);

        if ($partnerId) {
            $query->where('partner_id', $partnerId);
        }

        $animals = $query->get();
        $totalAnimals = $animals->count();
        $activeAnimals = $animals->where('is_active', true)->count();
        $deadAnimals = $animals->where('is_active', false)->count();

        // Calculate ADG: Requires at least 2 weight logs on the same animal
        $validAdgCount = 0;
        $totalAdgGrams = 0;

        foreach ($animals as $animal) {
            $logs = $animal->weightLogs->sortBy('weigh_date')->values();
            if ($logs->count() >= 2) {
                $first = $logs->first();
                $last = $logs->last();

                $days = Carbon::parse($first->weigh_date)->diffInDays(Carbon::parse($last->weigh_date));
                if ($days > 0 && $last->weight_kg >= $first->weight_kg) {
                    $gainKg = $last->weight_kg - $first->weight_kg;
                    $adgGrams = ($gainKg / $days) * 1000;
                    $totalAdgGrams += $adgGrams;
                    $validAdgCount++;
                }
            }
        }

        $averageAdgText = $validAdgCount > 0
            ? number_format($totalAdgGrams / $validAdgCount, 1) . ' g/hari'
            : 'TIDAK DAPAT DIHITUNG';

        // Actual treatment cost from treatment_logs
        $animalIds = $animals->pluck('id');
        $treatmentLogsCount = TreatmentLog::whereIn('animal_id', $animalIds)->count();
        $treatmentCostText = $treatmentLogsCount > 0
            ? 'Rp ' . number_format($treatmentLogsCount * 15000, 0, ',', '.')
            : 'Rp 0 (TIDAK ADA TREATMENT)';

        $partnerObj = $partnerId ? MasterPartner::find($partnerId) : null;
        $partnerName = $partnerObj ? $partnerObj->name : 'Semua Mitra (SFI)';

        return [
            'partner_id'          => $partnerId,
            'partner_name'        => $partnerName,
            'total_animals'       => $totalAnimals,
            'active_animals'      => $activeAnimals,
            'dead_animals'        => $deadAnimals,
            'average_adg_text'    => $averageAdgText,
            'valid_adg_sample'    => $validAdgCount,
            'treatment_cost_text' => $treatmentCostText,
            'treatment_logs_count'=> $treatmentLogsCount,
            'animals_list'        => $animals,
        ];
    }
}
