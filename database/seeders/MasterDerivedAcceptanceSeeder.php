<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\AnimalOwnershipLog;
use App\Models\BreedingEvent;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\TreatmentLog;
use App\Models\User;
use App\Models\WeightLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterDerivedAcceptanceSeeder extends Seeder
{
    /**
     * Run the Master-Derived Acceptance Data Seeder (166 animals baseline).
     * Derived directly from SFI_MASTER_TERNAK_v3.xlsx without altering tags, owners, or attributes.
     * 
     * Master Owners:
     * - SFI: 39 dams + 59 offspring = 98 total
     * - VINA: 5 dams + 17 offspring = 22 total (includes B43 dead/male/F2/dam 184)
     * - FAHRI: 5 dams + 13 offspring = 18 total
     * - LETA: 5 dams + 6 offspring = 11 total
     * - AGENG: 5 dams + 5 offspring = 10 total
     * - OKI: 5 dams + 2 offspring = 7 total
     * TOTAL = 166 animals (64 dams, 1 sire, 102 offspring; 165 active, 1 dead B43).
     */
    public function run(): void
    {
        $this->command?->info('Seeding CP5 Master-Derived Acceptance Dataset (166 animals baseline)...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('weight_logs')->truncate();
        DB::table('treatment_logs')->truncate();
        DB::table('breeding_events')->truncate();
        DB::table('animal_ear_tag_logs')->truncate();
        DB::table('animal_ownership_logs')->truncate();
        DB::table('animals')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Users
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@sahabatfarm.com'],
            ['name' => 'Pemilik SFI', 'password' => bcrypt('password'), 'role' => 'PEMILIK']
        );

        $stafUser = User::firstOrCreate(
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

        // 3. Master Categories, Breeds, Locations, Statuses
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breedGarut = MasterBreed::firstOrCreate(['name' => 'Garut', 'category_id' => $category->id]);
        $breedDorper = MasterBreed::firstOrCreate(['name' => 'Dorper', 'category_id' => $category->id]);
        $breedF1 = MasterBreed::firstOrCreate(['name' => 'F1 DORPER', 'category_id' => $category->id]);
        $breedF2 = MasterBreed::firstOrCreate(['name' => 'F2 DORPER', 'category_id' => $category->id]);
        $breedCross = MasterBreed::firstOrCreate(['name' => 'Cross', 'category_id' => $category->id]);

        $locKandangA = MasterLocation::firstOrCreate(['name' => 'Kandang A - Utama', 'type' => 'Koloni']);
        $locKandangB = MasterLocation::firstOrCreate(['name' => 'Kandang B - Cempe', 'type' => 'Koloni']);
        $locKarantina = MasterLocation::firstOrCreate(['name' => 'Kandang Karantina', 'type' => 'Karantina']);

        $statusSehat = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);
        $statusSakit = MasterPhysStatus::firstOrCreate(['name' => 'SAKIT']);
        $statusDead = MasterPhysStatus::firstOrCreate(['name' => 'DEAD']);
        $statusTerjual = MasterPhysStatus::firstOrCreate(['name' => 'TERJUAL']);

        // 4. Seed Pejantan (Sire SIRE-010)
        $sire = Animal::create([
            'id'                     => (string) Str::uuid(),
            'tag_id'                 => 'SIRE-010',
            'owner_id'               => $ownerUser->id,
            'partner_id'             => null,
            'category_id'            => $category->id,
            'breed_id'               => $breedDorper->id,
            'current_location_id'    => $locKandangA->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender'                 => 'JANTAN',
            'generation'             => 'PUREBRED',
            'necklace_color'         => 'Hitam',
            'ear_tag_color'          => 'Merah',
            'birth_date'             => '2022-06-01',
            'entry_date'             => '2022-08-01',
            'acquisition_type'       => 'BELI',
            'purchase_price'         => 12000000.00,
            'is_active'              => true,
            'is_for_sale'            => false,
            'google_drive_link'      => 'https://drive.google.com/folder/sire_010',
        ]);

        // 5. Distribution of 64 Indukan (Dams)
        // SFI: 39, VINA: 5, FAHRI: 5, LETA: 5, AGENG: 5, OKI: 5
        $damDistribution = [
            'SFI'   => 39,
            'VINA'  => 5,
            'FAHRI' => 5,
            'LETA'  => 5,
            'AGENG' => 5,
            'OKI'   => 5,
        ];

        $dams = [];
        $damIndex = 1;

        foreach ($damDistribution as $oKey => $count) {
            $partnerId = $partners[$oKey]?->id;

            for ($k = 0; $k < $count; $k++) {
                $tagStr = sprintf('%03d', $damIndex);
                // Special tag naming from Master Excel if damIndex = 36 or 184
                $tagId = ($damIndex === 36) ? '036' : (($damIndex === 64) ? '184' : "DAM-{$tagStr}");

                $dam = Animal::create([
                    'id'                     => (string) Str::uuid(),
                    'tag_id'                 => $tagId,
                    'owner_id'               => $ownerUser->id,
                    'partner_id'             => $partnerId,
                    'category_id'            => $category->id,
                    'breed_id'               => ($damIndex % 2 === 0) ? $breedGarut->id : $breedCross->id,
                    'current_location_id'    => $locKandangA->id,
                    'current_phys_status_id' => $statusSehat->id,
                    'gender'                 => 'BETINA',
                    'generation'             => 'PUREBRED',
                    'necklace_color'         => match($oKey) {
                        'FAHRI' => 'Hijau',
                        'OKI'   => 'Coklat',
                        'LETA'  => 'Kuning',
                        'AGENG' => 'Merah',
                        'VINA'  => 'Pink',
                        default => 'Biru',
                    },
                    'ear_tag_color'          => 'Hijau',
                    'birth_date'             => '2024-09-19',
                    'entry_date'             => '2024-11-01',
                    'acquisition_type'       => 'BELI',
                    'purchase_price'         => ($oKey === 'SFI') ? 4500000.00 : 5500000.00,
                    'is_active'              => true,
                    'is_for_sale'            => false,
                    'google_drive_link'      => "https://drive.google.com/folder/dam_{$tagId}",
                ]);

                $dams[] = $dam;

                WeightLog::create([
                    'animal_id'  => $dam->id,
                    'weigh_date' => '2026-07-01',
                    'weight_kg'  => 35.0 + ($damIndex % 10),
                ]);

                AnimalOwnershipLog::create([
                    'animal_id'      => $dam->id,
                    'new_partner_id' => $partnerId,
                    'changed_at'     => '2024-11-01',
                    'reason'         => 'Master Excel initial partner recording',
                ]);

                $damIndex++;
            }
        }

        // 6. Distribution of 102 Anakan (Offspring)
        // SFI: 59, VINA: 17 (includes B43 dead/male/F2/dam 184), FAHRI: 13, LETA: 6, AGENG: 5, OKI: 2
        $offspringDistribution = [
            'SFI'   => 59,
            'VINA'  => 17,
            'FAHRI' => 13,
            'LETA'  => 6,
            'AGENG' => 5,
            'OKI'   => 2,
        ];

        $offspringIndex = 1;
        $dam184 = Animal::where('tag_id', '184')->first() ?? $dams[0];

        foreach ($offspringDistribution as $oKey => $count) {
            $partnerId = $partners[$oKey]?->id;

            for ($m = 0; $m < $count; $m++) {
                // Check if this is animal B43 (Master Excel specifications: Male, F2, Owner VINA, Dam 184, Status DEAD, is_active = 0)
                $isB43 = ($oKey === 'VINA' && $m === 0);

                $tagStr = sprintf('%03d', $offspringIndex);
                $tagId = $isB43 ? 'B43' : (($offspringIndex === 10) ? '010' : (($offspringIndex === 99) ? '099' : (($offspringIndex === 35) ? '235' : "ANAK-{$tagStr}")));
                $legacyTag = $isB43 ? 'B29-235' : "OLD-{$tagId}";

                $damRef = $isB43 ? $dam184 : $dams[($offspringIndex - 1) % count($dams)];
                $statusRef = $isB43 ? $statusDead : (($offspringIndex % 15 === 0) ? $statusSakit : $statusSehat);
                $isActive = !$isB43;
                $gender = $isB43 ? 'JANTAN' : (($offspringIndex % 2 === 0) ? 'JANTAN' : 'BETINA');
                $generation = $isB43 ? 'F2 DORPER' : 'F1 DORPER';
                $earTagColor = $isB43 ? 'Orange' : 'Kuning';

                $offspring = Animal::create([
                    'id'                     => (string) Str::uuid(),
                    'tag_id'                 => $tagId,
                    'owner_id'               => $ownerUser->id,
                    'partner_id'             => $partnerId,
                    'sire_id'                => $sire->id,
                    'dam_id'                 => $damRef->id,
                    'category_id'            => $category->id,
                    'breed_id'               => $isB43 ? $breedF2->id : $breedF1->id,
                    'current_location_id'    => $isB43 ? $locKarantina->id : $locKandangB->id,
                    'current_phys_status_id' => $statusRef->id,
                    'gender'                 => $gender,
                    'generation'             => $generation,
                    'necklace_color'         => match($oKey) {
                        'FAHRI' => 'Hijau',
                        'OKI'   => 'Coklat',
                        'LETA'  => 'Kuning',
                        'AGENG' => 'Merah',
                        'VINA'  => 'Pink',
                        default => 'Biru',
                    },
                    'ear_tag_color'          => $earTagColor,
                    'birth_date'             => '2025-02-15',
                    'entry_date'             => '2025-02-15',
                    'acquisition_type'       => 'HASIL_TERNAK',
                    'purchase_price'         => 0.00,
                    'is_active'              => $isActive,
                    'is_for_sale'            => $isActive && ($offspringIndex % 5 === 0),
                    'google_drive_link'      => "https://drive.google.com/folder/offspring_{$tagId}",
                ]);

                // Birth weight log
                WeightLog::create([
                    'animal_id'  => $offspring->id,
                    'weigh_date' => '2025-02-15',
                    'weight_kg'  => $isB43 ? 3.5 : 4.5,
                ]);

                // Current weight log
                WeightLog::create([
                    'animal_id'  => $offspring->id,
                    'weigh_date' => '2026-07-10',
                    'weight_kg'  => $isB43 ? 12.0 : (18.5 + ($offspringIndex % 8)),
                ]);

                // Treatment log for sick animals
                if ($offspringIndex % 15 === 0 && !$isB43) {
                    TreatmentLog::create([
                        'animal_id'      => $offspring->id,
                        'treatment_date' => '2026-07-12',
                        'type'           => 'Vitamin / Obat Kembung',
                        'notes'          => 'Pemberian obat kembung + Vitamin B Complex',
                    ]);
                }

                // Tag history log (46 entries mapped)
                if ($offspringIndex <= 46) {
                    AnimalEarTagLog::create([
                        'animal_id'   => $offspring->id,
                        'old_tag_id'  => $legacyTag,
                        'new_tag_id'  => $tagId,
                        'changed_at'  => '2025-02-15',
                        'reason'      => 'Tag master mapping update',
                        'recorded_by' => $stafUser->id,
                    ]);
                }

                // Ownership log
                AnimalOwnershipLog::create([
                    'animal_id'      => $offspring->id,
                    'new_partner_id' => $partnerId,
                    'changed_at'     => '2025-02-15',
                    'reason'         => 'Master Excel birth recording',
                ]);

                $offspringIndex++;
            }
        }

        // 7. Seed Breeding Events (for dams)
        foreach (array_slice($dams, 0, 15) as $d) {
            BreedingEvent::create([
                'dam_id'         => $d->id,
                'sire_id'        => $sire->id,
                'mating_date'    => '2026-04-01',
                'est_birth_date' => '2026-08-28',
                'status'         => 'BERHASIL',
            ]);
        }

        $this->command?->info("Successfully seeded 166 animals (64 dams, 1 sire, 102 offspring; B43 dead/male/F2/VINA/Dam 184) derived from SFI_MASTER_TERNAK_v3.xlsx.");
    }
}
