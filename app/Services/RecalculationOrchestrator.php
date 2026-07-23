<?php

namespace App\Services;

use App\Events\SourceDataCorrected;
use App\Models\Animal;
use App\Models\DerivedCalculationRun;
use Illuminate\Support\Facades\Cache;

class RecalculationOrchestrator
{
    protected GrowthCalculationService $growthService;
    protected HppAllocationService $hppService;

    public function __construct(
        GrowthCalculationService $growthService,
        HppAllocationService $hppService
    ) {
        $this->growthService = $growthService;
        $this->hppService = $hppService;
    }

    /**
     * Handle source data correction event and trigger recalculation.
     */
    public function handleCorrection(SourceDataCorrected $event): DerivedCalculationRun
    {
        $run = DerivedCalculationRun::create([
            'trigger_correlation' => $event->correlationId,
            'affected_entity_type' => 'ANIMAL',
            'affected_entity_id' => $event->animalId,
            'affected_date_range' => $event->effectiveDate ?? date('Y-m-d'),
            'formula_version' => '1.0',
            'status' => 'STARTED',
            'started_at' => now(),
        ]);

        try {
            $animal = Animal::find($event->animalId);

            if ($animal) {
                // 1. Invalidate caches for old and new partners
                if ($event->oldPartnerId) {
                    Cache::forget("partner_report_data_{$event->oldPartnerId}");
                    Cache::forget("dashboard_metrics_{$event->oldPartnerId}");
                }
                if ($animal->partner_id) {
                    Cache::forget("partner_report_data_{$animal->partner_id}");
                    Cache::forget("dashboard_metrics_{$animal->partner_id}");
                }

                // 2. Re-calculate ADG
                $this->growthService->calculateForAnimal($animal);

                // 3. Re-calculate HPP
                $this->hppService->rebuildAnimalHpp($animal);

                // 4. Rebuild HPP allocations if entry date or partner changed
                if (in_array('entry_date', $event->changedFields) || in_array('partner_id', $event->changedFields)) {
                    $this->hppService->rebuildAllHpp();
                }
            }

            $checksum = md5("RUN_{$run->id}_SUCCESS");
            $run->update([
                'status' => 'COMPLETED',
                'result_checksum' => $checksum,
                'completed_at' => now(),
            ]);

        } catch (\Throwable $e) {
            $run->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }

        return $run;
    }
}
