<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\WeightLog;
use App\Models\ExitLog;
use App\Models\MasterPartner;
use Faker\Factory as Faker;
use Carbon\Carbon;

class SimulationHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Generate Historical Weight Logs for EXISTING Live Animals
        $animals = Animal::where('is_active', true)->get();
        $this->command->info('Generating weight logs for ' . $animals->count() . ' animals...');

        foreach ($animals as $animal) {
            // Assume entry date was birth date or purchase date
            $startDate = Carbon::parse($animal->entry_date);
            $now = Carbon::now();
            
            // Create a log every month from start date until now
            $currentDate = $startDate->copy();
            
            // Initial weight (already created in AnimalController store, but simulation seeder didn't create it in DB table, just 0 or ignored)
            // SimulationSeeder didn't create WeightLog. So we create one for "Entry".
            $currentWeight = ($animal->age_days < 90) ? rand(3, 10) : rand(20, 40); // Rough estimate
            
            // Log 1: Entry
            WeightLog::firstOrCreate([
                'animal_id' => $animal->id,
                'weigh_date' => $startDate->format('Y-m-d')
            ], [
                'weight_kg' => $currentWeight,
                'created_at' => $startDate,
                'updated_at' => $startDate
            ]);

            while ($currentDate->lessThan($now)) {
                $currentDate->addMonth();
                if ($currentDate->greaterThan($now)) break;

                // ADG simulation (0.1 - 0.3 kg/day)
                $days = 30;
                $dailyGain = $animal->daily_adg > 0 ? $animal->daily_adg : 0.15;
                $currentWeight += ($days * $dailyGain);

                WeightLog::create([
                    'animal_id' => $animal->id,
                    'weigh_date' => $currentDate->format('Y-m-d'),
                    'weight_kg' => round($currentWeight, 2),
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate
                ]);
            }
        }

        // 2. Generate "Ghost" Animals (Dead/Sold) for Financial Charts
        $partners = MasterPartner::all();
        $this->command->info('Generating historical exits (sales/deaths) for ' . $partners->count() . ' partners...');

        foreach ($partners as $partner) {
            // Generate 10-20 historical exits per partner
            $exitCount = rand(10, 20);

            for ($i = 0; $i < $exitCount; $i++) {
                // Event Date: Random in last 6 months
                $exitDate = Carbon::now()->subDays(rand(1, 180));
                
                // Create a "Ghost" Animal
                $animal = Animal::create([
                    'tag_id' => 'HIST-' . strtoupper($faker->bothify('??####')),
                    'gender' => rand(0, 1) ? 'MALE' : 'FEMALE',
                    'birth_date' => $exitDate->copy()->subMonths(rand(6, 24)),
                    'entry_date' => $exitDate->copy()->subMonths(rand(3, 12)),
                    'breed_id' => 1, // Default
                    'category_id' => 1, // Default
                    'current_location_id' => 1,
                    'current_phys_status_id' => 6, // Sold/Dead
                    'is_active' => false, // Inactive
                    'acquisition_type' => 'BOUGHT',
                    'purchase_price' => rand(1500000, 2500000),
                    'partner_id' => $partner->id,
                    'daily_adg' => 0.2
                ]);

                // Determine Exit Type
                $type = (rand(1, 10) > 2) ? 'SALE' : 'DEATH'; // 80% Sale, 20% Death

                if ($type === 'SALE') {
                    $price = rand(3000000, 5000000);
                    ExitLog::create([
                        'animal_id' => $animal->id,
                        'exit_date' => $exitDate,
                        'exit_type' => 'SALE',
                        'price' => $price,
                        'final_hpp' => rand(2000000, 2800000), // Cost
                        'created_at' => $exitDate,
                        'updated_at' => $exitDate
                    ]);
                } else {
                    ExitLog::create([
                        'animal_id' => $animal->id,
                        'exit_date' => $exitDate,
                        'exit_type' => 'DEATH',
                        'price' => 0,
                        'final_hpp' => rand(1500000, 2500000), // Loss
                        'created_at' => $exitDate,
                        'updated_at' => $exitDate
                    ]);
                }
            }
        }
    }
}
