<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\DataQualityIssue;
use App\Models\User;
use Illuminate\Support\Str;

class MissingDataGovernanceService
{
    /**
     * Evaluate an animal's missing fields and manage DataQualityIssues idempotently.
     */
    public function evaluateAnimal(Animal $animal): array
    {
        $issues = [];

        // Rule 1: Eartag
        if (empty($animal->tag_id) || str_starts_with($animal->tag_id, 'TEMP-')) {
            $issues[] = $this->createOrUpdateIssue([
                'idempotency_key' => "MISSING_TAG_{$animal->id}",
                'record_type' => 'ANIMAL',
                'record_id' => $animal->id,
                'tag_id' => $animal->tag_id,
                'category' => 'IDENTITAS',
                'field_name' => 'tag_id',
                'description' => 'Nomor eartag final belum diisi.',
                'severity' => 'CRITICAL_FOR_FINALIZATION',
                'blocked_processes' => ['FINAL_IDENTIFICATION', 'MERGE'],
                'assigned_role' => 'STAF',
                'remediation_url' => "/animals/{$animal->id}/edit",
                'evidence_needed' => 'Foto eartag terpasang',
            ]);
        }

        // Rule 2: Birth Date
        if (empty($animal->birth_date)) {
            $issues[] = $this->createOrUpdateIssue([
                'idempotency_key' => "MISSING_BIRTH_DATE_{$animal->id}",
                'record_type' => 'ANIMAL',
                'record_id' => $animal->id,
                'tag_id' => $animal->tag_id,
                'category' => 'TANGGAL_LAHIR',
                'field_name' => 'birth_date',
                'description' => 'Tanggal lahir belum diisi.',
                'severity' => 'CONDITIONALLY_REQUIRED',
                'blocked_processes' => ['AGE_CATEGORY', 'AGE_BASED_ADG', 'BREEDING_ELIGIBILITY'],
                'assigned_role' => 'STAF',
                'remediation_url' => "/animals/{$animal->id}/edit",
                'evidence_needed' => 'Catatan kelahiran / konfirmasi pemilik',
            ]);
        }

        // Rule 3: Gender
        if (empty($animal->gender) || !in_array($animal->gender, ['JANTAN', 'BETINA'])) {
            $issues[] = $this->createOrUpdateIssue([
                'idempotency_key' => "MISSING_GENDER_{$animal->id}",
                'record_type' => 'ANIMAL',
                'record_id' => $animal->id,
                'tag_id' => $animal->tag_id,
                'category' => 'JENIS_KELAMIN',
                'field_name' => 'gender',
                'description' => 'Jenis kelamin belum diketahui/dipilih.',
                'severity' => 'CRITICAL_FOR_FINALIZATION',
                'blocked_processes' => ['BREEDING_ROLE', 'GENERATION_RULES'],
                'assigned_role' => 'STAF',
                'remediation_url' => "/animals/{$animal->id}/edit",
                'evidence_needed' => 'Pemeriksaan fisik ternak',
            ]);
        }

        // Rule 4: Dam
        if (empty($animal->dam_id)) {
            $issues[] = $this->createOrUpdateIssue([
                'idempotency_key' => "MISSING_DAM_{$animal->id}",
                'record_type' => 'ANIMAL',
                'record_id' => $animal->id,
                'tag_id' => $animal->tag_id,
                'category' => 'INDUK',
                'field_name' => 'dam_id',
                'description' => 'Induk (dam) belum diketahui.',
                'severity' => 'OPTIONAL',
                'blocked_processes' => ['LITTER_PERFORMANCE', 'GENEALOGY'],
                'assigned_role' => 'STAF',
                'remediation_url' => "/animals/{$animal->id}/edit",
                'evidence_needed' => 'Catatan silsilah / tes DNA',
            ]);
        }

        // Rule 5: Weight
        if ($animal->weightLogs()->count() === 0) {
            $issues[] = $this->createOrUpdateIssue([
                'idempotency_key' => "MISSING_WEIGHT_{$animal->id}",
                'record_type' => 'ANIMAL',
                'record_id' => $animal->id,
                'tag_id' => $animal->tag_id,
                'category' => 'PENIMBANGAN',
                'field_name' => 'weight',
                'description' => 'Belum ada data penimbangan aktual.',
                'severity' => 'CONDITIONALLY_REQUIRED',
                'blocked_processes' => ['ADG_CALCULATION', 'WEIGHT_TREND', 'PRICE_PER_KG'],
                'assigned_role' => 'STAF',
                'remediation_url' => "/animals/{$animal->id}/weight-logs/create",
                'evidence_needed' => 'Hasil timbangan aktual',
            ]);
        }

        return array_filter($issues);
    }

    /**
     * Create or update a DataQualityIssue idempotently.
     */
    public function createOrUpdateIssue(array $data): DataQualityIssue
    {
        return DataQualityIssue::updateOrCreate(
            ['idempotency_key' => $data['idempotency_key']],
            array_merge($data, [
                'status' => 'OPEN',
                'audit_trail' => [
                    ['action' => 'EVALUATED', 'timestamp' => now()->toIso8601String(), 'actor' => 'SYSTEM']
                ]
            ])
        );
    }

    /**
     * Check if a specific operational process is blocked for an animal.
     */
    public function isProcessBlocked(string $processName, ?Animal $animal = null): bool
    {
        $query = DataQualityIssue::where('status', 'OPEN');

        if ($animal) {
            $query->where(function ($q) use ($animal) {
                $q->where('record_id', $animal->id)->orWhere('tag_id', $animal->tag_id);
            });
        }

        $openIssues = $query->get();

        foreach ($openIssues as $issue) {
            $blocked = $issue->blocked_processes ?? [];
            if (in_array($processName, $blocked, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Complete data for an issue ("Lengkapi Data" workflow).
     */
    public function completeData(string $issueId, array $inputData, User $user): DataQualityIssue
    {
        $issue = DataQualityIssue::findOrFail($issueId);

        $beforeState = $issue->toArray();

        // If animal record ID present, apply updates
        $animalId = $issue->animal_id ?? ($issue->record_type === 'ANIMAL' ? $issue->record_id : null);
        if ($animalId) {
            $animal = Animal::find($animalId);
            if ($animal && $issue->field_name && isset($inputData[$issue->field_name])) {
                $animal->update([$issue->field_name => $inputData[$issue->field_name]]);
            }
        }

        $auditTrail = $issue->audit_trail ?? [];
        $auditTrail[] = [
            'action' => 'USER_COMPLETED',
            'timestamp' => now()->toIso8601String(),
            'actor' => $user->email,
            'input' => $inputData,
        ];

        $issue->update([
            'status' => 'RESOLVED',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
            'audit_trail' => $auditTrail,
        ]);

        return $issue;
    }

    /**
     * Scan all active animals and generate DataQualityIssues for any missing fields.
     */
    public function scanAndGenerateIssues(): int
    {
        $animals = Animal::where('is_active', true)->get();
        $total = 0;
        foreach ($animals as $animal) {
            $issues = $this->evaluateAnimal($animal);
            $total += count($issues);
        }
        return $total;
    }

    /**
     * Check whether a process is allowed for a given animal.
     *
     * @return array{is_blocked: bool, blocking_issue_codes: array<string>}
     */
    public function checkProcessAllowed(string $animalId, string $processName): array
    {
        $animal = Animal::find($animalId);

        $openIssues = DataQualityIssue::where('status', 'OPEN')
            ->where(function ($q) use ($animalId, $animal) {
                $q->where('record_id', $animalId)
                  ->orWhere('animal_id', $animalId);
                if ($animal) {
                    $q->orWhere('tag_id', $animal->tag_id);
                }
            })
            ->get();

        $blockingCodes = [];
        foreach ($openIssues as $issue) {
            $blocked = $issue->blocked_processes ?? [];
            if (in_array($processName, $blocked, true)) {
                $blockingCodes[] = $issue->issue_code ?? $issue->idempotency_key ?? $issue->field_name;
            }
        }

        return [
            'is_blocked'            => count($blockingCodes) > 0,
            'blocking_issue_codes'  => $blockingCodes,
        ];
    }

    /**
     * Resolve a DataQualityIssue by applying a corrected value and marking it resolved.
     */
    public function resolveIssue(string $issueId, string $correctedValue, string $userId): bool
    {
        $issue = DataQualityIssue::find($issueId);

        if (!$issue) {
            return false;
        }

        // Apply corrected value to the animal field — check both record_id and animal_id
        $animalLookupId = $issue->record_id ?? $issue->animal_id ?? null;
        if ($animalLookupId && $issue->field_name) {
            $animal = Animal::find($animalLookupId);
            if ($animal) {
                $animal->update([$issue->field_name => $correctedValue]);
            }
        }

        $auditTrail = $issue->audit_trail ?? [];
        $auditTrail[] = [
            'action'    => 'USER_RESOLVED',
            'timestamp' => now()->toIso8601String(),
            'actor'     => $userId,
            'value'     => $correctedValue,
        ];

        $issue->update([
            'status'      => 'RESOLVED',
            'resolved_by' => $userId,
            'resolved_at' => now(),
            'audit_trail' => $auditTrail,
        ]);

        return true;
    }
}
