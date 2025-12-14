<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MasterCategory;
use App\Models\MasterBreed;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\Animal;
use App\Models\WeightLog;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $owner = User::create([
            'name' => 'Pak Budi',
            'email' => 'owner@sahabat-farm.com',
            'password' => bcrypt('password'),
            'role' => 'OWNER',
        ]);

        $staff = User::create([
            'name' => 'Mas Joko',
            'email' => 'staff@sahabat-farm.com',
            'password' => bcrypt('password'),
            'role' => 'STAFF',
        ]);

        // 2. Master Data
        $catSheep = MasterCategory::create(['name' => 'Domba']);
        $catGoat = MasterCategory::create(['name' => 'Kambing']);

        $breedDorper = MasterBreed::create(['category_id' => $catSheep->id, 'name' => 'Dorper']);
        $breedGarut = MasterBreed::create(['category_id' => $catSheep->id, 'name' => 'Garut']);
        $breedBoer = MasterBreed::create(['category_id' => $catGoat->id, 'name' => 'Boer']);

        $locIndividual = MasterLocation::create(['name' => 'Kandang Individu A', 'type' => 'Kandang Individu']);
        $locColony = MasterLocation::create(['name' => 'Kandang Koloni 1', 'type' => 'Kandang Koloni']);

        $statCempe = MasterPhysStatus::create(['name' => 'Cempe']);
        $statGrower = MasterPhysStatus::create(['name' => 'Pembesaran']);
        $statReady = MasterPhysStatus::create(['name' => 'Siap Kawin']);

        // 3. Inventory
        $feedConcentrate = InventoryItem::create(['name' => 'Konsentrat Premium', 'unit' => 'sak', 'current_stock' => 100]);
        $feedForage = InventoryItem::create(['name' => 'Rumput Odot', 'unit' => 'kg', 'current_stock' => 5000]);

        // Purchases (Backdated)
        InventoryPurchase::create([
            'item_id' => $feedConcentrate->id,
            'date' => Carbon::now()->subMonths(2),
            'qty' => 50,
            'price_total' => 50 * 350000, // 350k per sak
        ]);

        // 4. Animals (50 head)
        $animals = [];
        for ($i = 0; $i < 50; $i++) {
            $isMale = rand(0, 1);
            $breed = rand(0, 1) ? $breedDorper : $breedGarut;
            $location = rand(0, 1) ? $locIndividual : $locColony;

            // Random birth date between 3-12 months ago
            $birthDate = Carbon::now()->subMonths(rand(3, 12));

            $animal = Animal::create([
                'tag_id' => 'SF-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'owner_id' => $owner->id,
                'category_id' => $breed->category_id,
                'breed_id' => $breed->id,
                'current_location_id' => $location->id,
                'current_phys_status_id' => $statGrower->id,
                'gender' => $isMale ? 'MALE' : 'FEMALE',
                'birth_date' => $birthDate,
                'acquisition_type' => 'BRED',
                'is_active' => true,
                'health_status' => 'HEALTHY',
                'current_hpp' => rand(1000000, 2000000), // Random starting HPP
            ]);

            $animals[] = $animal;

            // Generate Weight Logs to simulate growth
            // Initial weight at birth/acquisition
            $initialWeight = rand(15, 20);
            WeightLog::create([
                'animal_id' => $animal->id,
                'weigh_date' => $birthDate,
                'weight_kg' => $initialWeight,
            ]);

            // Weight 1 month ago
            $weight1 = $initialWeight + rand(3, 5);
            $date1 = $birthDate->copy()->addMonth();
            if ($date1 < Carbon::now()) {
                WeightLog::create([
                    'animal_id' => $animal->id,
                    'weigh_date' => $date1,
                    'weight_kg' => $weight1,
                ]);
            }

            // Current weight
            $currentWeight = $weight1 + rand(3, 5);
            $log = WeightLog::create([
                'animal_id' => $animal->id,
                'weigh_date' => Carbon::now(),
                'weight_kg' => $currentWeight,
            ]);

            // Trigger ADG calculation for the last log
            // Note: In real seeder, we might want to manually set adg or call the action.
            // Since we registered a model event in Booted, it *should* trigger if we were running in app.
            // But just in case, let's update ADG manually here to ensure data looks good.
            $days = $date1->diffInDays(Carbon::now());
            if ($days > 0) {
                 $adg = ($currentWeight - $weight1) / $days;
                 $animal->update(['daily_adg' => $adg]);
            }
        }
    }
}
