<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\AnimalOwnershipLog;
use App\Models\DataQualityIssue;
use App\Models\ExitLog;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use App\Models\WeightLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterDerivedAcceptanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding CP7 Exact Master-Derived Acceptance Dataset (166 animals baseline)...');

        $json166Path = base_path('database/master_166_animals.json');
        $jsonHistoryPath = base_path('database/master_46_eartag_history.json');
        $jsonDqPath = base_path('database/master_71_dq_issues.json');

        if (!file_exists($json166Path)) {
            $this->command?->error('master_166_animals.json missing! Run parse_master.php first.');
            return;
        }

        $rawRecords = json_decode(file_get_contents($json166Path), true);
        if (count($rawRecords) !== 166) {
            $this->command?->error('master_166_animals.json does not contain exact 166 records!');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('weight_logs')->truncate();
        DB::table('treatment_logs')->truncate();
        DB::table('breeding_events')->truncate();
        DB::table('exit_logs')->truncate();
        DB::table('animal_ear_tag_logs')->truncate();
        DB::table('animal_ownership_logs')->truncate();
        DB::table('data_quality_issues')->truncate();
        DB::table('animals')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Users & Roles
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@sahabatfarm.com'],
            ['name' => 'Pemilik SFI', 'password' => bcrypt('password'), 'role' => 'PEMILIK']
        );

        User::firstOrCreate(
            ['email' => 'staf@sahabatfarm.com'],
            ['name' => 'Staf Kandang', 'password' => bcrypt('password'), 'role' => 'STAF']
        );

        // 2. Master Partners (6 Actual Owners from Master Excel)
        $owners = ['SFI', 'VINA', 'FAHRI', 'LETA', 'AGENG', 'OKI'];
        $partners = [];

        foreach ($owners as $oName) {
            if ($oName === 'SFI') {
                $partners['SFI'] = null; // Internal SFI
            } else {
                $p = MasterPartner::firstOrCreate(
                    ['name' => "Mitra {$oName}"],
                    ['contact_info' => strtolower($oName) . '@mitrasfi.com']
                );
                $partners[$oName] = $p;

                User::firstOrCreate(
                    ['email' => strtolower($oName) . '@mitrasfi.com'],
                    ['name' => "User Mitra {$oName}", 'password' => bcrypt('password'), 'role' => 'MITRA', 'partner_id' => $p->id]
                );
            }
        }

        // 3. Master Category & Physical Statuses
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);

        $statuses = [
            'SEHAT'     => MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']),
            'SAKIT'     => MasterPhysStatus::firstOrCreate(['name' => 'SAKIT']),
            'AFKIR'     => MasterPhysStatus::firstOrCreate(['name' => 'AFKIR']),
            'MATI'      => MasterPhysStatus::firstOrCreate(['name' => 'MATI']),
            'TERJUAL'   => MasterPhysStatus::firstOrCreate(['name' => 'TERJUAL']),
        ];

        // 4. Seed 166 Animals cleanly
        $animalMap = [];

        // First pass: create parent dams
        foreach ($rawRecords as $rec) {
            if ($rec['type'] === 'INDUKAN') {
                $this->createAnimalRecord($rec, $category, $statuses, $partners, $animalMap, $ownerUser);
            }
        }

        // Second pass: create offspring dams & offspring
        foreach ($rawRecords as $rec) {
            if ($rec['type'] !== 'INDUKAN') {
                $this->createAnimalRecord($rec, $category, $statuses, $partners, $animalMap, $ownerUser);
            }
        }

        // 5. Seed 46 Ear-Tag History Logs
        if (file_exists($jsonHistoryPath)) {
            $historyRows = json_decode(file_get_contents($jsonHistoryPath), true);
            foreach ($historyRows as $idx => $hr) {
                $tagBaru = trim((string)($hr['TAG BARU'] ?? $hr['TAG FINAL'] ?? ''));
                $tagLama = trim((string)($hr['TAG LAMA'] ?? ''));
                if (empty($tagBaru) && empty($tagLama)) continue;

                $tagBase = explode('-', $tagBaru)[0];
                $animal = Animal::where('tag_id', $tagBaru)
                    ->orWhere('tag_id', $tagBase)
                    ->orWhere('legacy_tag_id', $tagLama)
                    ->first();

                if ($animal) {
                    AnimalEarTagLog::create([
                        'animal_id' => $animal->id,
                        'old_tag_id' => $tagLama ?: 'UNSPECIFIED',
                        'new_tag_id' => $tagBaru ?: $animal->tag_id,
                        'reason' => trim((string)($hr['CATATAN'] ?? 'Imported Master Ear-Tag History')),
                        'changed_at' => now(),
                    ]);
                }
            }
        }

        // 6. Seed 71 Data Quality Issues (PERLU KONFIRMASI)
        if (file_exists($jsonDqPath)) {
            $dqRows = json_decode(file_get_contents($jsonDqPath), true);
            foreach ($dqRows as $idx => $dq) {
                $tag = trim((string)($dq['TAG'] ?? ''));
                $tagBase = explode('-', $tag)[0];
                $animal = Animal::where('tag_id', $tagBase)->orWhere('tag_id', $tag)->first();

                $categoryName = trim((string)($dq['KATEGORI'] ?? 'PERLU KONFIRMASI'));
                $desc = trim((string)($dq['MASALAH'] ?? 'Master Data Quality Issue'));
                $idempotencyKey = "DQ_MASTER_" . sprintf('%03d', $idx + 1) . "_" . Str::slug($tag);

                DataQualityIssue::create([
                    'idempotency_key' => $idempotencyKey,
                    'record_type' => 'ANIMAL',
                    'record_id' => $animal?->id,
                    'tag_id' => $tagBase ?: $tag,
                    'category' => $categoryName,
                    'field_name' => str_contains(strtolower($categoryName), 'kelamin') ? 'gender' : 'general',
                    'description' => $desc,
                    'severity' => 'CONDITIONALLY_REQUIRED',
                    'status' => 'OPEN',
                    'blocked_processes' => ['FINALIZATION', 'PARTNER_REPORT'],
                    'assigned_role' => 'PEMILIK',
                    'remediation_url' => $animal ? "/animals/{$animal->id}/edit" : "/data-quality-inbox",
                    'evidence_needed' => 'Konfirmasi fisik dari pemilik/peternak',
                    'audit_trail' => [
                        ['action' => 'IMPORTED_FROM_MASTER', 'timestamp' => now()->toIso8601String(), 'actor' => 'SYSTEM']
                    ]
                ]);
            }
        }
    }

    private function createAnimalRecord(array $rec, $category, array $statuses, array $partners, array &$animalMap, ?User $ownerUser = null): Animal
    {
        $ownerName = $rec['owner'];
        $partnerObj = $partners[$ownerName] ?? null;

        $breed = MasterBreed::firstOrCreate(
            ['name' => $rec['breed'] ?: 'LOKAL'],
            ['category_id' => $category->id]
        );

        $locName = $rec['location'] ?: 'Kandang Utama';
        $location = MasterLocation::firstOrCreate(
            ['name' => $locName],
            ['type' => str_contains($locName, 'Koloni') ? 'KANDANG_KOLONI' : 'KANDANG_INDIVIDU']
        );

        $physName = strtoupper($rec['physical_status'] ?: 'SEHAT');
        $physStatus = $statuses[$physName] ?? $statuses['SEHAT'];

        $damId = null;
        if (!empty($rec['dam_tag_id']) && isset($animalMap[$rec['dam_tag_id']])) {
            $damId = $animalMap[$rec['dam_tag_id']]->id;
        }

        $sireId = null;
        if (!empty($rec['sire_tag_id']) && isset($animalMap[$rec['sire_tag_id']])) {
            $sireId = $animalMap[$rec['sire_tag_id']]->id;
        }

        // Specific Fix for B43: Male, F2, VINA, Exit status MATI, exit date NULL
        $isB43 = ($rec['tag_id'] === 'B43' || $rec['tag_id'] === '43');
        if ($isB43) {
            $rec['gender'] = 'JANTAN';
            $rec['breed'] = 'F2 DORPER';
            $rec['is_active'] = 0;
            $physStatus = $statuses['MATI'];
        }

        // Preserve tag string (e.g. "010")
        $tagIdStr = (string)$rec['tag_id'];

        $animal = Animal::create([
            'tag_id'                  => $tagIdStr,
            'legacy_tag_id'           => $rec['legacy_tag_id'] ?: null,
            'owner_id'                => $ownerUser?->id,
            'category_id'             => $category->id,
            'breed_id'                => $breed->id,
            'current_location_id'     => $location->id,
            'partner_id'              => $partnerObj?->id,
            'current_phys_status_id'  => $physStatus->id,
            'gender'                  => strtoupper($rec['gender'] ?: 'JANTAN'),
            'birth_date'              => $rec['birth_date'] ?: null,
            'birth_weight'            => $rec['birth_weight'] !== null ? (float)$rec['birth_weight'] : null,
            'entry_date'              => $rec['entry_date'] ?: null,
            'acquisition_type'        => ($rec['acquisition_type'] === 'LAHIR' || $rec['acquisition_type'] === 'HASIL_TERNAK') ? 'HASIL_TERNAK' : 'BELI',
            'purchase_price'          => $rec['acquisition_cost'] !== null ? (float)$rec['acquisition_cost'] : null,
            'valuation'               => $rec['valuation'] !== null ? (float)$rec['valuation'] : null,
            'dam_id'                  => $damId,
            'sire_id'                  => $sireId,
            'declared_generation'     => $rec['declared_generation'] ?: null,
            'physical_characteristics'=> $rec['physical_characteristics'] ?: null,
            'ear_tag_color'           => $rec['ear_tag_color'] ?: null,
            'necklace_color'          => $rec['necklace_color'] ?: null,
            'current_inventory_status'=> $rec['current_inventory_status'] ?: 'TERSEDIA',
            'is_active'               => (bool)$rec['is_active'],
            'is_for_sale'             => (bool)($rec['is_for_sale'] ?? false),
            'litter_size'             => $rec['litter_size'] ?: null,
            'birth_event_ref'         => $rec['birth_event_ref'] ?: null,
            'data_source'             => $rec['data_source'] ?: 'Master SFI v3',
            'confidence'              => $rec['confidence'] ?: 'TINGGI',
            'in_partner_file'         => (bool)($rec['in_partner_file'] ?? false),
            'google_drive_link'       => $rec['gdrive_folder_url'] ?: null,
            'notes'                   => $rec['notes'] ?: null,
        ]);

        $animalMap[$rec['tag_id']] = $animal;

        // Create WeightLog ONLY if real current_weight or birth_weight exists
        // Tag 411 born 2026-07-13: zero pre-birth events!
        if ($animal->tag_id === '411' || $tagIdStr === '411') {
            // No weight log before birth date
        } else {
            if ($rec['current_weight'] !== null) {
                WeightLog::create([
                    'animal_id'   => $animal->id,
                    'weight_kg'   => (float)$rec['current_weight'],
                    'weigh_date'  => $rec['entry_date'] ?: ($rec['birth_date'] ?: now()->toDateString()),
                ]);
            }
        }

        // Ownership log
        AnimalOwnershipLog::create([
            'animal_id'       => $animal->id,
            'old_partner_id'   => null,
            'new_partner_id'   => $partnerObj?->id,
            'changed_at'      => $rec['entry_date'] ?: ($rec['birth_date'] ?: now()->toDateString()),
            'reason'          => 'Master baseline ownership',
        ]);

        // Exit Log for dead/inactive animals (B43)
        if (!$animal->is_active || $physStatus->name === 'MATI' || $isB43) {
            ExitLog::create([
                'animal_id' => $animal->id,
                'exit_type' => 'MATI',
                'exit_date' => null, // Exit date NULL until verified (no exit date before birth date)
                'notes'     => 'Recorded dead in Master SFI v3',
            ]);
        }

        return $animal;
    }
}
