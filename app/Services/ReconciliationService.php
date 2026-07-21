<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\ReconciliationLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReconciliationService
{
    /**
     * Compare uploaded rows against database.
     * Read-only — returns diff, never modifies data.
     */
    public function compare(Collection $uploadedRows): array
    {
        $batchId = (string) Str::uuid();
        $results = [];
        $now = now();

        // Index uploaded rows by tag_id
        $uploadedByTag = $uploadedRows->keyBy('tag_id');

        // Get all active animals with their current tag_id
        $allAnimals = Animal::with(['earTagLogs'])->get();
        $animalsByUuid = $allAnimals->keyBy('id');
        $animalsByTag = $allAnimals->keyBy('tag_id');

        // Track which DB animals were matched
        $matchedAnimalIds = [];

        // --- PASS 1: Match uploaded rows to DB animals ---
        foreach ($uploadedRows as $row) {
            $tagId = $row['tag_id'] ?? null;
            if (!$tagId) {
                $results[] = $this->makeLog($batchId, 'UNCERTAIN', null, null, null, null, null, 0.3, 'Baris tanpa tag_id');
                continue;
            }

            $animal = null;
            $matchMethod = '';
            $confidence = 0.0;

            // Strategy A: Match by UUID (if row has id)
            if (!empty($row['id'])) {
                $animal = $animalsByUuid->get($row['id']);
                if ($animal) {
                    $matchMethod = 'uuid';
                    $confidence = 1.0;
                }
            }

            // Strategy B: Match by active tag_id
            if (!$animal) {
                $animal = $animalsByTag->get($tagId);
                if ($animal) {
                    $matchMethod = 'tag_id';
                    $confidence = 1.0;
                }
            }

            // Strategy C: Match via ear tag history
            if (!$animal) {
                $tagLog = AnimalEarTagLog::where('new_tag_id', $tagId)
                    ->orWhere('old_tag_id', $tagId)
                    ->first();
                if ($tagLog) {
                    $animal = $animalsByUuid->get($tagLog->animal_id);
                    $matchMethod = 'tag_history';
                    $confidence = 0.9;
                }
            }

            // Strategy D: Match by tag_id + partner_id
            if (!$animal && !empty($row['partner_id'])) {
                $animal = Animal::where('tag_id', $tagId)
                    ->where('partner_id', $row['partner_id'])
                    ->first();
                if ($animal) {
                    $matchMethod = 'tag_partner';
                    $confidence = 0.8;
                }
            }

            if (!$animal) {
                // EXCEL_ONLY — exists in upload but not in DB
                $results[] = $this->makeLog($batchId, 'EXCEL_ONLY', null, $tagId, null, null, null, 0.5, 'Tidak ditemukan di database');
                continue;
            }

            $matchedAnimalIds[] = $animal->id;

            // Compare fields
            $fields = [
                'birth_date', 'gender', 'generation', 'ear_tag_color',
                'necklace_color', 'purchase_price', 'sale_price',
                'partner_id', 'current_location_id', 'breed_id',
                'google_drive_link', 'is_active',
            ];

            $hasDiff = false;
            foreach ($fields as $field) {
                $dbVal = $animal->$field;
                $uploadVal = $row[$field] ?? null;

                // Skip fields not provided in upload (null = not in spreadsheet)
                if ($uploadVal === null || $uploadVal === '') {
                    continue;
                }

                // Normalize dates
                if ($field === 'birth_date') {
                    $uploadVal = \Carbon\Carbon::parse($uploadVal)->format('Y-m-d');
                    $dbVal = $animal->birth_date?->format('Y-m-d');
                }

                // Normalize booleans
                if (in_array($field, ['is_active'])) {
                    $dbVal = $dbVal ? '1' : '0';
                    $uploadVal = $uploadVal ? '1' : '0';
                }

                // Normalize numeric values (DB stores 0.00, upload may have 0)
                if (is_numeric($uploadVal) && is_numeric($dbVal)) {
                    $uploadVal = (float) $uploadVal;
                    $dbVal = (float) $dbVal;
                }

                if ((string) $uploadVal !== (string) $dbVal) {
                    $hasDiff = true;
                    $results[] = $this->makeLog(
                        $batchId, 'CONFLICT', $animal->id, $tagId,
                        $field, $dbVal, $uploadVal, $confidence,
                        "Cocok via {$matchMethod}"
                    );
                }
            }

            if (!$hasDiff) {
                $results[] = $this->makeLog(
                    $batchId, 'SAME', $animal->id, $tagId,
                    null, null, null, $confidence,
                    "Cocok via {$matchMethod}"
                );
            }
        }

        // --- PASS 2: Find WEB_ONLY animals (in DB but not in upload) ---
        $uploadedTagIds = $uploadedRows->pluck('tag_id')->filter();
        foreach ($allAnimals as $animal) {
            if (!in_array($animal->id, $matchedAnimalIds)) {
                $results[] = $this->makeLog(
                    $batchId, 'WEB_ONLY', $animal->id, $animal->tag_id,
                    null, null, null, 1.0,
                    'Ada di database tapi tidak di file upload'
                );
            }
        }

        // Persist all logs
        ReconciliationLog::insert($results);

        return [
            'batch_id' => $batchId,
            'timestamp' => $now,
            'summary' => $this->summarize($results),
            'results' => $results,
        ];
    }

    /**
     * Get all reconciliation batches.
     */
    public function getBatches(): Collection
    {
        return ReconciliationLog::select('batch_id')
            ->selectRaw('MIN(created_at) as created_at')
            ->selectRaw("SUM(CASE WHEN status='SAME' THEN 1 ELSE 0 END) as same_count")
            ->selectRaw("SUM(CASE WHEN status='CONFLICT' THEN 1 ELSE 0 END) as conflict_count")
            ->selectRaw("SUM(CASE WHEN status='WEB_ONLY' THEN 1 ELSE 0 END) as web_only_count")
            ->selectRaw("SUM(CASE WHEN status='EXCEL_ONLY' THEN 1 ELSE 0 END) as excel_only_count")
            ->selectRaw("SUM(CASE WHEN status='UNCERTAIN' THEN 1 ELSE 0 END) as uncertain_count")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('batch_id')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get diff for a specific batch.
     */
    public function getBatchDiff(string $batchId): Collection
    {
        return ReconciliationLog::byBatch($batchId)
            ->orderBy('created_at')
            ->get();
    }

    private function makeLog(
        string $batchId, string $status, ?string $animalId, ?string $tagId,
        ?string $field, mixed $oldVal, mixed $newVal,
        float $confidence, string $notes = ''
    ): array {
        return [
            'id' => (string) Str::uuid(),
            'batch_id' => $batchId,
            'status' => $status,
            'animal_id' => $animalId,
            'tag_id' => $tagId,
            'field' => $field,
            'old_value' => $oldVal !== null ? (string) $oldVal : null,
            'new_value' => $newVal !== null ? (string) $newVal : null,
            'confidence' => $confidence,
            'notes' => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function summarize(array $results): array
    {
        $counts = ['SAME' => 0, 'CONFLICT' => 0, 'WEB_ONLY' => 0, 'EXCEL_ONLY' => 0, 'UNCERTAIN' => 0];
        foreach ($results as $r) {
            $counts[$r['status']]++;
        }
        return $counts;
    }
}