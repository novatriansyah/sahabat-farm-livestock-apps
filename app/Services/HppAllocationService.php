<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\HppAllocation;
use App\Models\HppManualCost;
use App\Models\TreatmentLog;
use Illuminate\Support\Facades\DB;

class HppAllocationService
{
    public const RULE_VERSION = '1.0';

    /**
     * Allocate a cost across eligible animals and create idempotent ledger entries.
     */
    public function allocateCost(
        string $sourceType,
        string $sourceId,
        string $effectiveDate,
        float $totalAmount,
        ?int $partnerId = null,
        ?int $locationId = null
    ): int {
        return DB::transaction(function () use ($sourceType, $sourceId, $effectiveDate, $totalAmount, $partnerId, $locationId) {
            // Find eligible active animals based on entry_date <= effectiveDate
            $query = Animal::where('is_active', true)
                ->where(function ($q) use ($effectiveDate) {
                    $q->whereNull('entry_date')
                        ->orWhere('entry_date', '<=', $effectiveDate);
                });

            if ($partnerId) {
                $query->where('partner_id', $partnerId);
            }
            if ($locationId) {
                $query->where('current_location_id', $locationId);
            }

            $eligibleAnimals = $query->get();
            if ($eligibleAnimals->isEmpty()) {
                return 0;
            }

            $perAnimalAmount = round($totalAmount / $eligibleAnimals->count(), 2);
            $allocatedCount = 0;

            foreach ($eligibleAnimals as $animal) {
                $idempotencyKey = "ALLOC_{$sourceType}_{$sourceId}_{$animal->id}_{$effectiveDate}";

                $existing = HppAllocation::where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    if ($existing->status === 'REVERSED') {
                        $existing->update(['status' => 'ACTIVE', 'amount' => $perAnimalAmount]);
                    }
                } else {
                    HppAllocation::create([
                        'animal_id' => $animal->id,
                        'source_type' => $sourceType,
                        'source_id' => (string) $sourceId,
                        'effective_date' => $effectiveDate,
                        'partner_id' => $animal->partner_id,
                        'location_id' => $animal->current_location_id,
                        'amount' => $perAnimalAmount,
                        'allocation_rule_version' => self::RULE_VERSION,
                        'idempotency_key' => $idempotencyKey,
                        'status' => 'ACTIVE',
                    ]);
                }
                $allocatedCount++;
            }

            return $allocatedCount;
        });
    }

    /**
     * Reverse all allocations for a given source.
     */
    public function reverseAllocationsForSource(string $sourceType, string $sourceId): int
    {
        return HppAllocation::where('source_type', $sourceType)
            ->where('source_id', (string) $sourceId)
            ->where('status', 'ACTIVE')
            ->update(['status' => 'REVERSED']);
    }

    /**
     * Rebuild HPP for a specific animal from active ledgers.
     */
    public function rebuildAnimalHpp(Animal $animal): float
    {
        // Add initial acquisition price if set
        $purchasePrice = (float) ($animal->purchase_price ?? 0);
        $activeLedgerSum = (float) HppAllocation::where('animal_id', $animal->id)
            ->where('status', 'ACTIVE')
            ->sum('amount');

        $totalHpp = round($purchasePrice + $activeLedgerSum, 2);
        $animal->update(['current_hpp' => $totalHpp]);

        return $totalHpp;
    }

    /**
     * Rebuild HPP for all animals in the system.
     */
    public function rebuildAllHpp(): array
    {
        return DB::transaction(function () {
            // Process TreatmentLogs costs
            $treatmentLogs = TreatmentLog::all();
            foreach ($treatmentLogs as $log) {
                // If treatment log has cost metadata or default treatment cost
                $cost = (float) ($log->cost ?? 0);
                if ($cost > 0) {
                    $idempotencyKey = "ALLOC_TREATMENT_{$log->id}_{$log->animal_id}_{$log->treatment_date}";
                    HppAllocation::updateOrCreate(
                        ['idempotency_key' => $idempotencyKey],
                        [
                            'animal_id' => $log->animal_id,
                            'source_type' => 'TREATMENT_LOG',
                            'source_id' => (string) $log->id,
                            'effective_date' => $log->treatment_date,
                            'amount' => $cost,
                            'allocation_rule_version' => self::RULE_VERSION,
                            'status' => 'ACTIVE',
                        ]
                    );
                }
            }

            // Process Manual Costs
            if (class_exists(HppManualCost::class)) {
                $manualCosts = HppManualCost::all();
                foreach ($manualCosts as $mc) {
                    $this->allocateCost(
                        'MANUAL_COST',
                        (string) $mc->id,
                        $mc->date ?? $mc->created_at->toDateString(),
                        (float) $mc->amount,
                        $mc->partner_id,
                        $mc->location_id
                    );
                }
            }

            // Update animal projections
            $animals = Animal::all();
            $checksumData = [];

            foreach ($animals as $animal) {
                $hpp = $this->rebuildAnimalHpp($animal);
                $checksumData[] = "{$animal->id}:{$hpp}";
            }

            $checksum = md5(implode('|', $checksumData));

            return [
                'animal_count' => $animals->count(),
                'ledger_count' => HppAllocation::where('status', 'ACTIVE')->count(),
                'checksum' => $checksum,
            ];
        });
    }
}
