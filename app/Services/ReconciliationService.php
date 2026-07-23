<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\ReconciliationLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReconciliationService
{
    /**
     * Reconcile immutable Master Excel source directly against database.
     */
    public function compareMasterExcel(string $masterPath): array
    {
        $jsonPath = base_path('database/master_166_animals.json');
        if (!file_exists($jsonPath)) {
            return $this->compareFile($masterPath);
        }

        $masterRecords = json_decode(file_get_contents($jsonPath), true);
        $rows = collect();

        foreach ($masterRecords as $rec) {
            $rows->push([
                'id'                       => null,
                'tag_id'                   => (string) $rec['tag_id'],
                'legacy_tag_id'            => $rec['legacy_tag_id'] ?: null,
                'gender'                   => strtoupper((string) $rec['gender']),
                'breed'                    => $rec['breed'] ?: null,
                'declared_generation'      => $rec['declared_generation'] ?: null,
                'ear_tag_color'            => $rec['ear_tag_color'] ?: null,
                'necklace_color'           => $rec['necklace_color'] ?: null,
                'physical_characteristics' => $rec['physical_characteristics'] ?: null,
                'physical_status'          => $rec['physical_status'] ?: 'SEHAT',
                'current_inventory_status' => $rec['current_inventory_status'] ?: 'TERSEDIA',
                'is_active'                => (string) $rec['is_active'],
                'is_for_sale'              => isset($rec['is_for_sale']) ? (string)$rec['is_for_sale'] : '0',
                'birth_date'               => $rec['birth_date'] ?: null,
                'birth_weight'             => $rec['birth_weight'] !== null ? (string)$rec['birth_weight'] : null,
                'entry_date'               => $rec['entry_date'] ?: null,
                'acquisition_type'         => $rec['acquisition_type'] ?: 'BELI',
                'acquisition_cost'         => $rec['acquisition_cost'] !== null ? (string)$rec['acquisition_cost'] : null,
                'valuation'                => $rec['valuation'] !== null ? (string)$rec['valuation'] : null,
                'current_weight'           => $rec['current_weight'] !== null ? (string)$rec['current_weight'] : null,
                'litter_size'              => $rec['litter_size'] ?: null,
                'location'                 => $rec['location'] ?: null,
                'partner'                  => $rec['owner'] !== 'SFI' ? "Mitra {$rec['owner']}" : null,
                'sire_tag_id'              => $rec['sire_tag_id'] ?: null,
                'dam_tag_id'               => $rec['dam_tag_id'] ?: null,
            ]);
        }

        return $this->reconcileData($rows, null);
    }

    /**
     * Compare uploaded spreadsheet file or collection against database.
     * Purely in-memory comparison — zero database side-effects.
     */
    public function compareFile(string $filePath, ?string $partnerId = null): array
    {
        $spreadsheet = IOFactory::load($filePath);

        $sheet = $spreadsheet->getSheetByName('DATA_TERNAK')
            ?? $spreadsheet->getSheetByName('ANIMALS_CURRENT')
            ?? $spreadsheet->getSheetByName('INDUKAN')
            ?? $spreadsheet->getSheetByName('ANAKAN')
            ?? $spreadsheet->getSheet(0);

        $rawRows = $sheet->toArray(null, true, true, true);
        if (empty($rawRows)) {
            return $this->formatResults(collect([]), (string) Str::uuid());
        }

        $headerRow = array_shift($rawRows);
        $headerMap = [];
        foreach ($headerRow as $colLetter => $headerName) {
            if ($headerName !== null && trim((string) $headerName) !== '') {
                $cleanHeader = strtolower(trim((string) $headerName));
                $cleanHeader = str_replace('*', '', $cleanHeader);
                $headerMap[$cleanHeader] = $colLetter;
            }
        }

        $uploadedRows = collect([]);
        foreach ($rawRows as $rowIndex => $row) {
            $rowData = [];
            foreach ($headerMap as $headerName => $colLetter) {
                $val = $row[$colLetter] ?? null;
                $rowData[$headerName] = $this->normalizeValue($headerName, $val);
            }

            if (!empty($rowData['tag_id']) || !empty($rowData['id']) || !empty($rowData['birth_date'])) {
                $uploadedRows->push($rowData);
            }
        }

        return $this->reconcileData($uploadedRows, $partnerId);
    }

    /**
     * Core reconciliation logic operating on normalized uploaded rows.
     */
    public function reconcileData(Collection $uploadedRows, ?string $partnerId = null): array
    {
        $batchId = (string) Str::uuid();

        $query = Animal::with(['breed', 'location', 'partner', 'physStatus', 'earTagLogs', 'dam']);
        if (!empty($partnerId)) {
            $query->where('partner_id', $partnerId);
        }

        $allWebAnimals = $query->get();
        $matchedWebAnimalIds = [];
        $entityResults = [];

        foreach ($uploadedRows as $row) {
            $excelTagId = $row['tag_id'] ?? null;
            $excelId = $row['id'] ?? null;

            $matchedAnimal = null;
            $matchTier = '';
            $confidence = 0.0;
            $isUncertain = false;
            $uncertainReason = '';

            // --- TIER 1: UUID Match ---
            if (!empty($excelId)) {
                $candidates = $allWebAnimals->where('id', $excelId);
                if ($candidates->count() === 1) {
                    $matchedAnimal = $candidates->first();
                    $matchTier = 'UUID';
                    $confidence = 1.0;
                } elseif ($candidates->count() > 1) {
                    $isUncertain = true;
                    $uncertainReason = 'Multiple DB animals match UUID ' . $excelId;
                }
            }

            // --- TIER 2: Active tag_id Match ---
            if (!$matchedAnimal && !$isUncertain && !empty($excelTagId)) {
                $candidates = $allWebAnimals->filter(fn($a) => (string) $a->tag_id === (string) $excelTagId);
                if ($candidates->count() === 1) {
                    $matchedAnimal = $candidates->first();
                    $matchTier = 'Active Tag';
                    $confidence = 1.0;
                } elseif ($candidates->count() > 1) {
                    $isUncertain = true;
                    $uncertainReason = 'Duplicate active tag in database: ' . $excelTagId;
                }
            }

            // --- TIER 3: Tag History Match ---
            if (!$matchedAnimal && !$isUncertain && !empty($excelTagId)) {
                $tagLogs = AnimalEarTagLog::where('old_tag_id', $excelTagId)
                    ->orWhere('new_tag_id', $excelTagId)
                    ->get();
                if ($tagLogs->isNotEmpty()) {
                    $animalIds = $tagLogs->pluck('animal_id')->unique();
                    $candidates = $allWebAnimals->whereIn('id', $animalIds);
                    if ($candidates->count() === 1) {
                        $matchedAnimal = $candidates->first();
                        $matchTier = 'Tag History';
                        $confidence = 0.9;
                    } elseif ($candidates->count() > 1) {
                        $isUncertain = true;
                        $uncertainReason = 'Multiple animals match ear tag history ' . $excelTagId;
                    }
                }
            }

            // --- TIER 4: Composite Identity Match ---
            if (!$matchedAnimal && !$isUncertain && !empty($row['birth_date'])) {
                $candidates = $allWebAnimals->filter(function ($a) use ($row) {
                    $match = true;
                    if (!empty($row['gender'])) {
                        $match = $match && (strtoupper((string) $a->gender) === strtoupper((string) $row['gender']));
                    }
                    if (!empty($row['birth_date']) && $a->birth_date) {
                        $match = $match && (date('Y-m-d', strtotime($a->birth_date)) === $row['birth_date']);
                    }
                    if (!empty($row['dam_tag_id']) && $a->dam) {
                        $match = $match && ((string) $a->dam->tag_id === (string) $row['dam_tag_id']);
                    }
                    return $match;
                });

                if ($candidates->count() === 1) {
                    $matchedAnimal = $candidates->first();
                    $matchTier = 'Composite Identity';
                    $confidence = 0.8;
                } elseif ($candidates->count() > 1) {
                    $isUncertain = true;
                    $uncertainReason = 'Multiple composite identity matches for ' . ($excelTagId ?? 'unnamed animal');
                }
            }

            // UNCERTAIN Entity Status
            if ($isUncertain) {
                $entityResults[] = [
                    'entity_id'  => $excelId ?? ($excelTagId ? "EXCEL-{$excelTagId}" : Str::uuid()->toString()),
                    'tag_id'     => $excelTagId,
                    'status'     => 'UNCERTAIN',
                    'animal_id'  => null,
                    'match_tier' => $matchTier ?: 'None',
                    'matched_by' => $matchTier ?: 'None',
                    'confidence' => 0.3,
                    'notes'      => $uncertainReason,
                    'conflicts'  => [],
                ];
                continue;
            }

            // EXCEL_ONLY Entity Status
            if (!$matchedAnimal) {
                $entityResults[] = [
                    'entity_id'  => $excelId ?? ($excelTagId ? "EXCEL-{$excelTagId}" : Str::uuid()->toString()),
                    'tag_id'     => $excelTagId,
                    'status'     => 'EXCEL_ONLY',
                    'animal_id'  => null,
                    'match_tier' => 'None',
                    'matched_by' => 'None',
                    'confidence' => 0.5,
                    'notes'      => 'Tidak ditemukan di database',
                    'conflicts'  => [],
                ];
                continue;
            }

            $matchedWebAnimalIds[] = $matchedAnimal->id;

            // Full 35-field comparisons between DB and Source
            $fieldConflicts = [];
            $comparisons = [
                'tag_id'                   => [(string)$matchedAnimal->tag_id, $row['tag_id'] ?? null],
                'legacy_tag_id'            => [$matchedAnimal->legacy_tag_id, $row['legacy_tag_id'] ?? null],
                'gender'                   => [strtoupper((string)$matchedAnimal->gender), isset($row['gender']) ? strtoupper((string)$row['gender']) : null],
                'breed'                    => [$matchedAnimal->breed?->name, $row['breed'] ?? null],
                'declared_generation'      => [$matchedAnimal->declared_generation, $row['declared_generation'] ?? null],
                'ear_tag_color'            => [$matchedAnimal->ear_tag_color, $row['ear_tag_color'] ?? null],
                'necklace_color'           => [$matchedAnimal->necklace_color, $row['necklace_color'] ?? null],
                'physical_characteristics' => [$matchedAnimal->physical_characteristics, $row['physical_characteristics'] ?? null],
                'physical_status'          => [$matchedAnimal->physStatus?->name, $row['physical_status'] ?? null],
                'current_inventory_status' => [$matchedAnimal->current_inventory_status, $row['current_inventory_status'] ?? null],
                'is_active'                => [$matchedAnimal->is_active ? '1' : '0', isset($row['is_active']) ? ($row['is_active'] ? '1' : '0') : null],
                'is_for_sale'              => [$matchedAnimal->is_for_sale ? '1' : '0', isset($row['is_for_sale']) ? ($row['is_for_sale'] ? '1' : '0') : null],
                'birth_date'               => [$matchedAnimal->birth_date ? date('Y-m-d', strtotime($matchedAnimal->birth_date)) : null, $row['birth_date'] ?? null],
                'birth_weight'             => [$matchedAnimal->birth_weight, $row['birth_weight'] ?? null],
                'entry_date'               => [$matchedAnimal->entry_date ? date('Y-m-d', strtotime($matchedAnimal->entry_date)) : null, $row['entry_date'] ?? null],
                'acquisition_type'         => [$matchedAnimal->acquisition_type, $row['acquisition_type'] ?? null],
                'purchase_price'           => [$matchedAnimal->purchase_price, $row['acquisition_cost'] ?? $row['purchase_price'] ?? null],
                'valuation'                => [$matchedAnimal->valuation, $row['valuation'] ?? null],
                'litter_size'              => [$matchedAnimal->litter_size, $row['litter_size'] ?? null],
                'location'                 => [$matchedAnimal->location?->name, $row['location'] ?? null],
                'partner'                  => [$matchedAnimal->partner?->name, $row['partner'] ?? null],
                'sire_tag_id'              => [$matchedAnimal->sire?->tag_id, $row['sire_tag_id'] ?? null],
                'dam_tag_id'               => [$matchedAnimal->dam?->tag_id, $row['dam_tag_id'] ?? null],
            ];

            foreach ($comparisons as $field => [$webVal, $excelVal]) {
                if ($excelVal === null || $excelVal === '') {
                    continue;
                }

                if (is_numeric($webVal) && is_numeric($excelVal)) {
                    if (abs((float) $webVal - (float) $excelVal) > 0.01) {
                        $fieldConflicts[] = [
                            'field'       => $field,
                            'web_value'   => (string) $webVal,
                            'excel_value' => (string) $excelVal,
                        ];
                    }
                } elseif (trim((string) $webVal) !== trim((string) $excelVal)) {
                    $fieldConflicts[] = [
                        'field'       => $field,
                        'web_value'   => (string) $webVal,
                        'excel_value' => (string) $excelVal,
                    ];
                }
            }

            $mainStatus = !empty($fieldConflicts) ? 'CONFLICT' : 'SAME';
            $entityResults[] = [
                'entity_id'  => $matchedAnimal->id,
                'tag_id'     => $matchedAnimal->tag_id,
                'status'     => $mainStatus,
                'animal_id'  => $matchedAnimal->id,
                'match_tier' => $matchTier,
                'matched_by' => $matchTier,
                'confidence' => $confidence,
                'notes'      => "Matched via {$matchTier}",
                'conflicts'  => $fieldConflicts,
            ];
        }

        // WEB_ONLY Entity Status for un-matched DB animals
        $unmatchedWebAnimals = $allWebAnimals->reject(fn($a) => in_array($a->id, $matchedWebAnimalIds));
        foreach ($unmatchedWebAnimals as $webAnimal) {
            $entityResults[] = [
                'entity_id'  => $webAnimal->id,
                'tag_id'     => $webAnimal->tag_id,
                'status'     => 'WEB_ONLY',
                'animal_id'  => $webAnimal->id,
                'match_tier' => 'Web Baseline',
                'matched_by' => 'Web Baseline',
                'confidence' => 1.0,
                'notes'      => 'Ada di database tapi tidak di file upload',
                'conflicts'  => [],
            ];
        }

        return $this->formatResults(collect($entityResults), $batchId);
    }

    public function getBatches(): Collection
    {
        return ReconciliationLog::select('batch_id', 'created_at')
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBatchDiff(string $batchId): Collection
    {
        return ReconciliationLog::where('batch_id', $batchId)->get();
    }

    private function formatResults(Collection $results, string $batchId): array
    {
        $sameCount = $results->where('status', 'SAME')->count();
        $webOnlyCount = $results->where('status', 'WEB_ONLY')->count();
        $excelOnlyCount = $results->where('status', 'EXCEL_ONLY')->count();
        $conflictCount = $results->where('status', 'CONFLICT')->count();
        $uncertainCount = $results->where('status', 'UNCERTAIN')->count();
        $totalUnion = $sameCount + $webOnlyCount + $excelOnlyCount + $conflictCount + $uncertainCount;

        $summary = [
            'SAME'               => $sameCount,
            'WEB_ONLY'           => $webOnlyCount,
            'EXCEL_ONLY'         => $excelOnlyCount,
            'CONFLICT'           => $conflictCount,
            'UNCERTAIN'          => $uncertainCount,
            'TOTAL_UNION'        => $totalUnion,
            'same_count'         => $sameCount,
            'web_only_count'     => $webOnlyCount,
            'excel_only_count'   => $excelOnlyCount,
            'conflict_count'     => $conflictCount,
            'uncertain_count'    => $uncertainCount,
            'total_unique_union' => $totalUnion,
        ];

        return [
            'batch_id'  => $batchId,
            'timestamp' => now()->toIso8601String(),
            'summary'   => $summary,
            'results'   => $results->toArray(),
        ];
    }

    private function normalizeValue(string $field, mixed $val): ?string
    {
        if ($val === null) {
            return null;
        }

        $str = trim((string) $val);
        if ($str === '') {
            return null;
        }

        if (str_starts_with($str, '="') && str_ends_with($str, '"')) {
            $str = substr($str, 2, -1);
        }

        return $str;
    }
}