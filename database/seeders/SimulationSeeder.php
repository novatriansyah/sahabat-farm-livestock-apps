<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPartner;
use App\Models\MasterLocation;
use App\Models\Animal;
use App\Models\User;
use App\Models\MasterBreed;
use App\Models\MasterPhysStatus;
use App\Models\MasterCategory;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Init Faker (Indonesia Locale)
        $faker = Faker::create('id_ID');

        // 2. Ensure basic data from SOP exists (Breeds, Statuses)
        // We fetch existing IDs to attach to animals
        $breeds = MasterBreed::pluck('id')->toArray();
        $sheepCatId = MasterCategory::where('name', 'Domba')->value('id') ?? 1;
        
        // Map Status Names to IDs for easier logic
        $statusMap = MasterPhysStatus::pluck('id', 'name')->toArray();
        // Fallback or defaults if names differ slightly
        $idCempe = $statusMap['Cempe Lahir'] ?? 1;
        $idSapih = $statusMap['Cempe Sapih'] ?? 2;
        $idDara = $statusMap['Dara'] ?? 3;
        $idBunting = $statusMap['Bunting'] ?? 4;
        $idMenyusui = $statusMap['Menyusui'] ?? 5;
        $idSiapJual = $statusMap['Penggemukan - Siap Jual'] ?? 6;
        
        // 3. Create Locations (Cages) - Simulate "Much locations"
        // Create 20 Cages/Locations
        $locationIds = [];
        $cageTypes = ['Kandang Koloni', 'Kandang Baterai', 'Kandang Isolasi', 'Kandang Umbaran'];
        
        for ($i = 1; $i <= 20; $i++) {
            $loc = MasterLocation::create([
                'name' => 'Kandang Blok ' . chr(64 + $i), // Blok A, B, C...
                'type' => $faker->randomElement($cageTypes)
            ]);
            $locationIds[] = $loc->id;
        }

        // 4. Create Partners (Dozens => 25 Partners)
        for ($p = 0; $p < 25; $p++) {
            $partner = MasterPartner::create([
                'name' => $faker->company . ' (Mitra ' . $faker->firstName . ')', // "PT Suka Maju (Mitra Budi)"
                'contact_info' => $faker->address . ' | ' . $faker->phoneNumber
            ]);

            // Randomly create a User for this Partner (50% chance)
            if (rand(0, 1)) {
                $email = strtolower(str_replace(' ', '.', $partner->name)) . '@example.com';
                // Sanitize email
                $email = preg_replace('/[^a-z0-9@.]/', '', $email);

                User::create([
                    'name' => 'Owner ' . $partner->name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'PARTNER',
                    'partner_id' => $partner->id
                ]);
            }

            // 5. Create Animals for this Partner (50 - 150 goats/sheep per partner)
            $animalCount = rand(50, 150);
            
            for ($a = 0; $a < $animalCount; $a++) {
                // Determine Age -> Status
                // 30% Kids, 50% Adult Females, 20% Adult Males
                $rand = rand(1, 100);
                
                $gender = 'FEMALE';
                $statusId = $idDara;
                $birthDate = Carbon::now();
                $weight = 0;

                if ($rand <= 30) {
                    // Kid (Cempe)
                    $gender = rand(0, 1) ? 'MALE' : 'FEMALE';
                    $ageDays = rand(1, 150);
                    $birthDate = Carbon::now()->subDays($ageDays);
                    $statusId = ($ageDays < 90) ? $idCempe : $idSapih;
                    $weight = rand(3, 15);
                } elseif ($rand <= 80) {
                    // Adult Female
                    $gender = 'FEMALE';
                    $ageMonths = rand(8, 48); // 8 months to 4 years
                    $birthDate = Carbon::now()->subMonths($ageMonths);
                    $weight = rand(25, 60);
                    
                    // Status logic
                    $sRand = rand(1, 100);
                    if ($sRand < 40) $statusId = $idBunting;
                    elseif ($sRand < 60) $statusId = $idMenyusui;
                    else $statusId = $idDara; // or Siap Kawin
                } else {
                    // Adult Male
                    $gender = 'MALE';
                    $ageMonths = rand(8, 60);
                    $birthDate = Carbon::now()->subMonths($ageMonths);
                    $weight = rand(35, 90);
                    $statusId = $idSiapJual; // Fattening
                }

                $animal = Animal::create([
                    'tag_id' => 'TAG-' . strtoupper($faker->unique()->bothify('??#####')), // Added # for more entropy
                    // 'name' => removed, column likely doesn't exist
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'entry_date' => $birthDate->copy()->addDays(rand(0, 30)),
                    'breed_id' => $breeds[array_rand($breeds)] ?? 1,
                    'category_id' => $sheepCatId,
                    'current_location_id' => $locationIds[array_rand($locationIds)],
                    'current_phys_status_id' => $statusId,
                    'is_active' => true,
                    'acquisition_type' => 'BOUGHT', // Required enum
                    'purchase_price' => rand(1000000, 3000000),
                    'partner_id' => $partner->id,
                    'daily_adg' => $faker->randomFloat(3, 0.1, 0.4),
                ]);

                // Create Initial Weight Log
                \App\Models\WeightLog::create([
                    'animal_id' => $animal->id,
                    'weigh_date' => $birthDate, // Set to birth/entry date
                    'weight_kg' => $weight,
                ]);
            }
        } // Close Partner Loop

        // 6. Generate Historical Data (Sales, Deaths, Expenses) for Dashboard Charts (Last 6 Months)
        $this->command->info('Seeding Historical Data (6 Months)...');
        $months = 6;
        $items = \App\Models\InventoryItem::all();
        // Fallback feed if empty
        $feedItem = $items->where('category', 'FEED')->first() ?? \App\Models\InventoryItem::create(['name' => 'Konsentrat Simulation', 'category' => 'FEED', 'stock' => 1000]);

        for ($i = $months; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            
            // A. Expenses (Purchases)
            for ($p = 0; $p < rand(3, 5); $p++) {
                \App\Models\InventoryPurchase::create([
                    'item_id' => $feedItem->id,
                    'date' => $date->copy()->addDays(rand(1, 28)),
                    'qty' => rand(10, 50),
                    'price_total' => rand(10, 50) * 350000,
                ]);
            }

            // B. Sales (Exits)
            for ($s = 0; $s < rand(2, 10); $s++) {
                $soldAnimal = Animal::create([
                    'tag_id' => 'SOLD-' . $faker->unique()->bothify('??####'),
                    'gender' => 'MALE',
                    'birth_date' => $date->copy()->subMonths(12),
                    'is_active' => false,
                    'health_status' => 'SOLD',
                    'partner_id' => MasterPartner::inRandomOrder()->first()->id ?? 1,
                    'acquisition_type' => 'BRED',
                    'breed_id' => $breeds[array_rand($breeds)] ?? 1,
                    'category_id' => $sheepCatId,
                    'current_location_id' => $locationIds[array_rand($locationIds)],
                    'current_phys_status_id' => $idSiapJual,
                ]);
                
                \App\Models\ExitLog::create([
                    'animal_id' => $soldAnimal->id,
                    'exit_date' => $date->copy()->addDays(rand(1, 28)),
                    'exit_type' => 'SALE',
                    'price' => rand(1500000, 3000000),
                    'price' => rand(1500000, 3000000),
                    'final_hpp' => 1000000,
                ]);
            }

            // C. Deaths (Exits)
            for ($d = 0; $d < rand(0, 3); $d++) {
                $deadAnimal = Animal::create([
                    'tag_id' => 'DIED-' . $faker->unique()->bothify('??####'),
                    'gender' => rand(0,1) ? 'MALE' : 'FEMALE',
                    'birth_date' => $date->copy()->subMonths(rand(1,6)),
                    'is_active' => false,
                    'health_status' => 'DECEASED',
                    'partner_id' => MasterPartner::inRandomOrder()->first()->id ?? 1,
                    'acquisition_type' => 'BRED',
                    'breed_id' => $breeds[array_rand($breeds)] ?? 1,
                    'category_id' => $sheepCatId,
                    'current_location_id' => $locationIds[array_rand($locationIds)],
                    'current_phys_status_id' => $idCempe,
                ]);

                \App\Models\ExitLog::create([
                    'animal_id' => $deadAnimal->id,
                    'exit_date' => $date->copy()->addDays(rand(1, 28)),
                    'exit_type' => 'DEATH',
                    'price' => 0,
                    'final_hpp' => 500000,
                ]);
            }
        }
    }
}
