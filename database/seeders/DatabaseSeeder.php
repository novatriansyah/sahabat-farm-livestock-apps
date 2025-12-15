<?php

namespace Database\Seeders;

use App\Models\User;
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

        // 2. Call SOP Seeder for Master Data (Breeds, Categories, Diseases, Inventory)
        $this->call(SopSeeder::class);

        // 3. Create Locations (Specific to Farm Instance, not SOP)
        $locIndividual = MasterLocation::create(['name' => 'Kandang Individu A', 'type' => 'Kandang Individu']);
        $locColony = MasterLocation::create(['name' => 'Kandang Koloni 1', 'type' => 'Kandang Koloni']);

        // 4. Additional Inventory Purchase (Seed Stock)
        $feedConcentrate = InventoryItem::where('name', 'like', '%Konsentrat%')->first();
        if ($feedConcentrate) {
            InventoryPurchase::create([
                'item_id' => $feedConcentrate->id,
                'date' => Carbon::now()->subMonths(2),
                'qty' => 50,
                'price_total' => 50 * 350000,
            ]);
        }

        // 5. Animals (50 head)
        $breedDorper = MasterBreed::where('name', 'Dorper')->first();
        $breedGarut = MasterBreed::where('name', 'Domba Garut')->first();

        // Fallback if seeder failed (shouldn't happen)
        if (!$breedDorper) $breedDorper = MasterBreed::first();
        if (!$breedGarut) $breedGarut = MasterBreed::first();

        // Statuses
        $statGrower = MasterPhysStatus::where('name', 'FATTENING')->first() ?? MasterPhysStatus::first();

        $animals = [];
        for ($i = 0; $i < 50; $i++) {
            $isMale = rand(0, 1);
            $breed = rand(0, 1) ? $breedDorper : $breedGarut;
            $location = rand(0, 1) ? $locIndividual : $locColony;

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
                'current_hpp' => rand(1000000, 2000000),
                'purchase_price' => 0,
            ]);

            $animals[] = $animal;

            // Generate Weight Logs
            $initialWeight = rand(15, 20);
            WeightLog::create([
                'animal_id' => $animal->id,
                'weigh_date' => $birthDate,
                'weight_kg' => $initialWeight,
            ]);

            $weight1 = $initialWeight + rand(3, 5);
            $date1 = $birthDate->copy()->addMonth();
            if ($date1 < Carbon::now()) {
                WeightLog::create([
                    'animal_id' => $animal->id,
                    'weigh_date' => $date1,
                    'weight_kg' => $weight1,
                ]);
            }

            $currentWeight = $weight1 + rand(3, 5);
            $log = WeightLog::create([
                'animal_id' => $animal->id,
                'weigh_date' => Carbon::now(),
                'weight_kg' => $currentWeight,
            ]);

            $days = $date1->diffInDays(Carbon::now());
            if ($days > 0) {
                 $adg = ($currentWeight - $weight1) / $days;
                 $animal->update(['daily_adg' => $adg]);
            }
        }
    }
}
