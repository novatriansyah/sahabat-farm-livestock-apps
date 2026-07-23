<?php

namespace App\Imports;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\AnimalOwnershipLog;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\WeightLog;
use App\Schemas\AnimalTemplateSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnimalsImport implements WithMultipleSheets
{
    public function __construct(
        private bool $dryRun = false,
        private ?string $partnerId = null
    ) {}

    public function sheets(): array
    {
        return [
            'DATA_TERNAK'     => new DataTernakSheetImport($this->dryRun, $this->partnerId),
            'ANIMALS_CURRENT' => new DataTernakSheetImport($this->dryRun, $this->partnerId),
            'INDUKAN'         => new DataTernakSheetImport($this->dryRun, $this->partnerId),
            'ANAKAN'          => new DataTernakSheetImport($this->dryRun, $this->partnerId),
        ];
    }

    public function collection(\Illuminate\Support\Collection $rows)
    {
        return (new DataTernakSheetImport($this->dryRun, $this->partnerId))->collection($rows);
    }
}

class DataTernakSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    public function __construct(
        private bool $dryRun = false,
        private ?string $partnerId = null
    ) {}

    public function collection(Collection $rows)
    {
        $currentUser = auth()->user();

        // On dry run, use read-only lookups
        $category = MasterCategory::where('name', 'Kambing')->first();
        if (!$category && !$this->dryRun) {
            $category = MasterCategory::create(['name' => 'Kambing']);
        }

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                // Ignore sample rows starting with [CONTOH]
                $tagIdRaw = trim((string) ($row['tag_id'] ?? $row['ear_tag'] ?? $row['tag'] ?? ''));
                if (str_starts_with($tagIdRaw, '[CONTOH]') || str_starts_with((string)($row['id'] ?? ''), '[CONTOH]')) {
                    $this->skippedCount++;
                    continue;
                }

                if ($tagIdRaw === '') {
                    $this->skippedCount++;
                    continue;
                }

                $tagId = $tagIdRaw;
                $uuid = trim((string) ($row['id'] ?? ''));
                if (str_starts_with($uuid, '[CONTOH]')) $uuid = '';

                $genderRaw = strtoupper(trim((string) ($row['gender'] ?? $row['jenis_kelamin'] ?? '')));
                $gender = str_contains($genderRaw, 'JANTAN') ? 'JANTAN' : (str_contains($genderRaw, 'BETINA') ? 'BETINA' : 'JANTAN');

                // Breed lookup
                $breedName = trim((string) ($row['breed'] ?? $row['breed_name'] ?? $row['ras'] ?? ''));
                $breed = null;
                if ($breedName !== '') {
                    $breed = MasterBreed::where('name', $breedName)->first();
                    if (!$breed && !$this->dryRun && $category) {
                        $breed = MasterBreed::create(['name' => $breedName, 'category_id' => $category->id]);
                    }
                }

                // Location lookup
                $locationName = trim((string) ($row['location'] ?? $row['location_name'] ?? $row['kandang'] ?? ''));
                $location = null;
                if ($locationName !== '') {
                    $location = MasterLocation::where('name', $locationName)->first();
                    if (!$location && !$this->dryRun) {
                        $location = MasterLocation::create([
                            'name' => $locationName,
                            'type' => str_contains($locationName, 'Koloni') ? 'KANDANG_KOLONI' : 'KANDANG_INDIVIDU'
                        ]);
                    }
                }

                // Physical Status lookup
                $physStatusName = strtoupper(trim((string) ($row['physical_status'] ?? $row['status_fisik'] ?? '')));
                $physStatus = null;
                if ($physStatusName !== '') {
                    $physStatus = MasterPhysStatus::where('name', $physStatusName)->first()
                        ?? MasterPhysStatus::where('name', 'like', "%{$physStatusName}%")->first();
                }

                // Partner lookup
                $partnerName = trim((string) ($row['partner'] ?? $row['partner_name'] ?? $row['pemilik'] ?? ''));
                $partnerIdResolved = $this->partnerId;
                if (!$partnerIdResolved && $partnerName !== '' && $partnerName !== 'SFI') {
                    $p = MasterPartner::where('name', $partnerName)->orWhere('name', "Mitra {$partnerName}")->first();
                    if ($p) $partnerIdResolved = $p->id;
                }

                // Birth Date — support Y-m-d, d-m-Y, Carbon objects, and full datetime strings
                $birthDateRaw = trim((string) ($row['birth_date'] ?? $row['tanggal_lahir'] ?? ''));
                $birthDate = null;
                if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthDateRaw)) {
                    $birthDate = $birthDateRaw;
                } elseif (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $birthDateRaw, $m)) {
                    $birthDate = "{$m[3]}-{$m[2]}-{$m[1]}";
                } elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})[ T]/', $birthDateRaw, $m)) {
                    // Full datetime string e.g. "2025-11-24 00:00:00"
                    $birthDate = "{$m[1]}-{$m[2]}-{$m[3]}";
                } elseif (is_numeric($birthDateRaw) && $birthDateRaw > 0) {
                    // Excel serial date
                    try {
                        $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$birthDateRaw)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $birthDate = null;
                    }
                }

                $acquisitionType = strtoupper(trim((string) ($row['acquisition_type'] ?? $row['cara_perolehan'] ?? 'BELI')));
                if (!in_array($acquisitionType, ['BELI', 'HASIL_TERNAK', 'MITRA'])) {
                    $acquisitionType = 'BELI';
                }

                $purchasePrice = isset($row['acquisition_cost']) && $row['acquisition_cost'] !== '' ? (float)$row['acquisition_cost'] : (isset($row['purchase_price']) ? (float)$row['purchase_price'] : null);
                $valuation = isset($row['valuation']) && $row['valuation'] !== '' ? (float)$row['valuation'] : null;
                $birthWeight = isset($row['birth_weight']) && $row['birth_weight'] !== '' ? (float)$row['birth_weight'] : null;
                $currentWeight = isset($row['current_weight']) && $row['current_weight'] !== '' ? (float)$row['current_weight'] : null;

                $isDead = ($physStatusName === 'DEAD' || $physStatusName === 'MATI' || $tagId === 'B43');
                $isActive = $isDead ? false : (isset($row['is_active']) ? (bool)$row['is_active'] : true);

                if ($this->dryRun) {
                    $this->importedCount++;
                    continue;
                }

                // Find animal by UUID or Tag ID
                $animal = null;
                if (!empty($uuid)) {
                    $animal = Animal::find($uuid);
                }
                if (!$animal) {
                    $animal = Animal::where('tag_id', $tagId)->first();
                }

                $attributes = [
                    'tag_id'                   => $tagId,
                    'legacy_tag_id'            => trim((string) ($row['legacy_tag_id'] ?? $row['tag_lama'] ?? '')) ?: null,
                    'owner_id'                 => $currentUser->id ?? Animal::first()?->owner_id ?? (string) Str::uuid(),
                    'partner_id'               => $partnerIdResolved,
                    'category_id'              => $category?->id ?? Animal::first()?->category_id ?? 1,
                    'breed_id'                 => $breed?->id ?? Animal::first()?->breed_id ?? 1,
                    'current_location_id'      => $location?->id ?? Animal::first()?->current_location_id ?? 1,
                    'current_phys_status_id'   => $physStatus?->id ?? Animal::first()?->current_phys_status_id ?? 1,
                    'gender'                   => $gender,
                    'declared_generation'      => trim((string) ($row['declared_generation'] ?? $row['generation'] ?? '')) ?: null,
                    'ear_tag_color'            => trim((string) ($row['ear_tag_color'] ?? '')) ?: null,
                    'necklace_color'           => trim((string) ($row['necklace_color'] ?? '')) ?: null,
                    'physical_characteristics' => trim((string) ($row['physical_characteristics'] ?? '')) ?: null,
                    'birth_date'               => $birthDate,
                    'birth_weight'             => $birthWeight,
                    'entry_date'               => trim((string) ($row['entry_date'] ?? '')) ?: null,
                    'acquisition_type'         => $acquisitionType,
                    'purchase_price'           => $purchasePrice,
                    'valuation'                => $valuation,
                    'current_inventory_status' => trim((string) ($row['current_inventory_status'] ?? '')) ?: ($isActive ? 'TERSEDIA' : 'KELUAR'),
                    'is_active'                => $isActive,
                    'is_for_sale'              => isset($row['is_for_sale']) ? (bool)$row['is_for_sale'] : false,
                    'litter_size'              => trim((string) ($row['litter_size'] ?? '')) ?: null,
                    'birth_event_ref'          => trim((string) ($row['birth_event_ref'] ?? '')) ?: null,
                    'data_source'              => trim((string) ($row['data_source'] ?? '')) ?: 'Import Excel',
                    'confidence'               => trim((string) ($row['confidence'] ?? '')) ?: 'TINGGI',
                    'in_partner_file'          => isset($row['in_partner_file']) ? (bool)$row['in_partner_file'] : false,
                    'google_drive_link'        => trim((string) ($row['gdrive_folder_url'] ?? $row['google_drive_link'] ?? '')) ?: null,
                    'notes'                    => trim((string) ($row['notes'] ?? '')) ?: null,
                ];

                if ($animal) {
                    $animal->update($attributes);
                    $this->updatedCount++;
                } else {
                    if (!empty($uuid)) {
                        $attributes['id'] = $uuid;
                    }
                    $animal = Animal::create($attributes);
                    $this->importedCount++;
                }

                // Log weight ONLY if currentWeight is provided
                if ($currentWeight !== null) {
                    $alreadyLogged = WeightLog::where('animal_id', $animal->id)
                        ->where('weight_kg', $currentWeight)
                        ->exists();

                    if (!$alreadyLogged) {
                        WeightLog::create([
                            'animal_id'  => $animal->id,
                            'weight_kg'  => $currentWeight,
                            'weigh_date' => now()->toDateString(),
                        ]);
                    }
                }
            }

            if ($this->dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = $e->getMessage();
            throw $e;
        }
    }
}
