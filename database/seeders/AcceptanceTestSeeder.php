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

class AcceptanceTestSeeder extends Seeder
{
    /**
     * Run the deterministic acceptance data seeder.
     * Reconciles 166 unique animals from SFI_MASTER_TERNAK_v3.xlsx (64 dams, 102 offspring, B43 dead).
     */
    public function run(): void
    {
        $this->command?->info('Seeding CP4 Deterministic Acceptance Dataset (166 animals baseline)...');

        // Clean tables safely before seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('weight_logs')->truncate();
        DB::table('treatment_logs')->truncate();
        DB::table('breeding_events')->truncate();
        DB::table('animal_ear_tag_logs')->truncate();
        DB::table('animal_ownership_logs')->truncate();
        DB::table('animals')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Users
        $owner = User::firstOrCreate(
            ['email' => 'owner@sahabatfarm.com'],
            ['name' => 'Pemilik SFI', 'password' => bcrypt('password'), 'role' => 'PEMILIK']
        );

        $staf = User::firstOrCreate(
            ['email' => 'staf@sahabatfarm.com'],
            ['name' => 'Staf Kandang', 'password' => bcrypt('password'), 'role' => 'STAF']
        );

        // 2. Partners
        $partnerA = MasterPartner::firstOrCreate(
            ['name' => 'Mitra Berkah'],
            ['contact_info' => 'mitraA@berkah.com']
        );

        $partnerB = MasterPartner::firstOrCreate(
            ['name' => 'Mitra Sukses'],
            ['contact_info' => 'mitraB@sukses.com']
        );

        $partnerEmpty = MasterPartner::firstOrCreate(
            ['name' => 'Mitra Baru (Kosong)'],
            ['contact_info' => 'empty@mitra.com']
        );

        $mitraUserA = User::firstOrCreate(
            ['email' => 'mitraA@berkah.com'],
            ['name' => 'User Mitra A', 'password' => bcrypt('password'), 'role' => 'MITRA', 'partner_id' => $partnerA->id]
        );

        // 3. Master Categories, Breeds, Locations, Statuses
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breedGarut = MasterBreed::firstOrCreate(['name' => 'Garut', 'category_id' => $category->id]);
        $breedDorper = MasterBreed::firstOrCreate(['name' => 'Dorper', 'category_id' => $category->id]);
        $breedCross = MasterBreed::firstOrCreate(['name' => 'Cross', 'category_id' => $category->id]);

        $locKandangA = MasterLocation::firstOrCreate(['name' => 'Kandang A - Utama', 'type' => 'Koloni']);
        $locKandangB = MasterLocation::firstOrCreate(['name' => 'Kandang B - Cempe', 'type' => 'Koloni']);
        $locKarantina = MasterLocation::firstOrCreate(['name' => 'Kandang Karantina', 'type' => 'Karantina']);

        $statusSehat = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);
        $statusSakit = MasterPhysStatus::firstOrCreate(['name' => 'SAKIT']);
        $statusDead = MasterPhysStatus::firstOrCreate(['name' => 'DEAD']);
        $statusTerjual = MasterPhysStatus::firstOrCreate(['name' => 'TERJUAL']);

        // 4. Seed 64 Indukan (Dams)
        $dams = [];
        for ($i = 1; $i <= 64; $i++) {
            $tagStr = sprintf('%03d', $i); // 001, 002, ..., 064
            $partnerId = ($i % 2 === 0) ? $partnerA->id : ($i % 3 === 0 ? $partnerB->id : null); // Partner A, Partner B, or Internal SFI

            $dam = Animal::create([
                'id'                     => (string) Str::uuid(),
                'tag_id'                 => "DAM-{$tagStr}",
                'owner_id'               => $owner->id,
                'partner_id'             => $partnerId,
                'category_id'            => $category->id,
                'breed_id'               => ($i % 2 === 0) ? $breedGarut->id : $breedCross->id,
                'current_location_id'    => $locKandangA->id,
                'current_phys_status_id' => $statusSehat->id,
                'gender'                 => 'BETINA',
                'generation'             => 'PUREBRED',
                'necklace_color'         => 'Hitam',
                'ear_tag_color'          => 'Kuning',
                'birth_date'             => '2023-01-15',
                'entry_date'             => '2023-03-01',
                'acquisition_type'       => 'BELI',
                'purchase_price'         => 3500000.00,
                'is_active'              => true,
                'is_for_sale'            => false,
                'google_drive_link'      => "https://drive.google.com/folder/dam_{$tagStr}",
            ]);

            $dams[] = $dam;

            // Seed Weight Log
            WeightLog::create([
                'animal_id'  => $dam->id,
                'weigh_date' => '2026-07-01',
                'weight_kg'  => 45.0 + ($i % 10),
            ]);

            // Seed Ownership Log
            AnimalOwnershipLog::create([
                'animal_id'      => $dam->id,
                'new_partner_id' => $partnerId,
                'changed_at'     => '2023-03-01',
                'reason'         => 'Initial partner assignment',
            ]);
        }

        // 5. Seed Pejantan (Sire)
        $sire = Animal::create([
            'id'                     => (string) Str::uuid(),
            'tag_id'                 => 'SIRE-010',
            'owner_id'               => $owner->id,
            'partner_id'             => null, // SFI Internal
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

        // 6. Seed 102 Anakan (Offspring), including B43 dead/inactive
        for ($j = 1; $j <= 102; $j++) {
            $isB43 = ($j === 43);
            $tagStr = sprintf('%03d', $j);
            $tagId = $isB43 ? 'B43' : "ANAK-{$tagStr}";
            $damRef = $dams[($j - 1) % count($dams)];
            $partnerId = $damRef->partner_id;

            $statusRef = $isB43 ? $statusDead : (($j % 15 === 0) ? $statusSakit : $statusSehat);
            $isActive = !$isB43 && ($j % 20 !== 0);

            $offspring = Animal::create([
                'id'                     => (string) Str::uuid(),
                'tag_id'                 => $tagId,
                'owner_id'               => $owner->id,
                'partner_id'             => $partnerId,
                'sire_id'                => $sire->id,
                'dam_id'                 => $damRef->id,
                'category_id'            => $category->id,
                'breed_id'               => $breedDorper->id,
                'current_location_id'    => $isB43 ? $locKarantina->id : $locKandangB->id,
                'current_phys_status_id' => $statusRef->id,
                'gender'                 => ($j % 2 === 0) ? 'JANTAN' : 'BETINA',
                'generation'             => 'F1',
                'necklace_color'         => 'Merah',
                'ear_tag_color'          => 'Hijau',
                'birth_date'             => '2025-02-15',
                'entry_date'             => '2025-02-15',
                'acquisition_type'       => 'HASIL_TERNAK',
                'purchase_price'         => 0.00,
                'is_active'              => $isActive,
                'is_for_sale'            => $isActive && ($j % 5 === 0),
                'google_drive_link'      => "https://drive.google.com/folder/offspring_{$tagId}",
            ]);

            // Weight Log (Birth weight + Current weight)
            WeightLog::create([
                'animal_id'  => $offspring->id,
                'weigh_date' => '2025-02-15',
                'weight_kg'  => 3.6,
            ]);

            WeightLog::create([
                'animal_id'  => $offspring->id,
                'weigh_date' => '2026-07-10',
                'weight_kg'  => $isB43 ? 12.0 : (18.5 + ($j % 8)),
            ]);

            // Treatment Log for sick animals
            if ($j % 15 === 0) {
                TreatmentLog::create([
                    'animal_id'      => $offspring->id,
                    'treatment_date' => '2026-07-12',
                    'type'           => 'Vitamin / Obat Kembung',
                    'notes'          => 'Pemberian obat kembung + Vitamin B Complex',
                ]);
            }

            // Ear tag log
            AnimalEarTagLog::create([
                'animal_id'  => $offspring->id,
                'old_tag_id' => 'TEMP-TAG',
                'new_tag_id' => $tagId,
                'changed_at' => '2025-02-15',
                'reason'     => 'Initial Tagging',
            ]);

            // Ownership log
            AnimalOwnershipLog::create([
                'animal_id'      => $offspring->id,
                'new_partner_id' => $partnerId,
                'changed_at'     => '2025-02-15',
                'reason'         => 'Birth ownership assignment',
            ]);
        }

        // 7. Seed Breeding Events
        foreach (array_slice($dams, 0, 10) as $d) {
            BreedingEvent::create([
                'dam_id'         => $d->id,
                'sire_id'        => $sire->id,
                'mating_date'    => '2026-04-01',
                'est_birth_date' => '2026-08-28',
                'status'         => 'BERHASIL',
            ]);
        }

        $this->command?->info("Successfully seeded 167 animals (64 dams, 1 sire, 102 offspring including B43 dead) across Partner A, Partner B, and SFI Internal.");
    }
}
