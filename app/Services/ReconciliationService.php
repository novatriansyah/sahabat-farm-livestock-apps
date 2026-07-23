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
     * Compare uploaded spreadsheet file or collection against database.
     * Purely in-memory comparison — zero database side-effects.
     */
    public function compareFile(string $filePath, ?string $partnerId = null): array
    {
        $spreadsheet = IOFactory::load($filePath);

        // 1. Read sheet by name priority, falling back to first sheet
        $sheet = $spreadsheet->getSheetByName('DATA_TERNAK')
            ?? $spreadsheet->getSheetByName('ANIMALS_CURRENT')
            ?? $spreadsheet->getSheetByName('INDUKAN')
            ?? $spreadsheet->getSheetByName('ANAKAN')
            ?? $spreadsheet->getSheet(0);

        $rawRows = $sheet->toArray(null, true, true, true);
        if (empty($rawRows)) {
            return $this->formatResults(collect([]), (string) Str::uuid());
        }

        // 2. Map header row (Row 1) to column indexes
        $headerRow = array_shift($rawRows);
        $headerMap = [];
        foreach ($headerRow as $colLetter => $headerName) {
            if ($headerName !== null && trim((string) $headerName) !== '') {
                $cleanHeader = strtolower(trim((string) $headerName));
                $cleanHeader = str_replace('*', '', $cleanHeader);
                $headerMap[$cleanHeader] = $colLetter;
            }
        }

        // 3. Process data rows into associative array by header names
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

        // 1. Query Web Baseline (Scoped strictly if partnerId provided)
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
                    'confidence' => 0.5,
                    'notes'      => 'Tidak ditemukan di database',
                    'conflicts'  => [],
                ];
                continue;
            }

            // Matched animal found
            $matchedWebAnimalIds[] = $matchedAnimal->id;

            // Compare fields between Web and Excel
            $fieldConflicts = [];
            $comparisons = [
                'tag_id'          => [$matchedAnimal->tag_id, $row['tag_id'] ?? null],
                'gender'          => [$matchedAnimal->gender, $row['gender'] ?? null],
                'birth_date'      => [$matchedAnimal->birth_date ? date('Y-m-d', strtotime($matchedAnimal->birth_date)) : null, $row['birth_date'] ?? null],
                'physical_status' => [$matchedAnimal->physStatus?->name, $row['physical_status'] ?? null],
                'is_active'       => [$matchedAnimal->is_active ? '1' : '0', isset($row['is_active']) ? ($row['is_active'] ? '1' : '0') : null],
                'purchase_price'  => [$matchedAnimal->purchase_price, $row['acquisition_cost'] ?? $row['purchase_price'] ?? null],
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
                'confidence' => $confidence,
                'notes'      => "Matched via {$matchTier}",
                'conflicts'  => $fieldConflicts,
            ];
        }

        // --- WEB_ONLY Entity Status for un-matched DB animals ---
        $unmatchedWebAnimals = $allWebAnimals->reject(fn($a) => in_array($a->id, $matchedWebAnimalIds));
        foreach ($unmatchedWebAnimals as $webAnimal) {
            $entityResults[] = [
                'entity_id'  => $webAnimal->id,
                'tag_id'     => $webAnimal->tag_id,
                'status'     => 'WEB_ONLY',
                'animal_id'  => $webAnimal->id,
                'match_tier' => 'Web Baseline',
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