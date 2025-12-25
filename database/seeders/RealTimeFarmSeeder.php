<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\MasterPartner;
use App\Models\WeightLog;
use App\Models\BreedingEvent;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use App\Models\InventoryUsageLog;
use App\Models\ExitLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealTimeFarmSeeder extends Seeder
{
    protected $timelineStart;
    protected $currentDate;
    protected $partners = [];
    protected $locations = [];
    protected $breeds = [];
    protected $statuses = [];
    protected $feedItems = [];

    public function run(): void
    {
        $this->command->info('Initializing Real-Time Farm Simulation...');

        // 1. Setup Timeline (Start 24 months ago)
        $this->timelineStart = Carbon::now()->subMonths(24);
        $this->currentDate = $this->timelineStart->copy();

        // 2. Load Masters
        $this->loadMasters();

        // 3. Create Partners
        $this->createPartners();

        // 4. Initial Stock Purchase (Month 0)
        $this->purchaseInitialStock();

        // 5. Run Simulation Loop (Month by Month)
        while ($this->currentDate->lessThanOrEqualTo(Carbon::now())) {
            $this->command->info("Simulating: " . $this->currentDate->format('F Y'));

            $this->runMonthlyEvents();

            // Advance Time
            $this->currentDate->addMonth();
        }

        $this->command->info('Simulation Complete!');
    }

    protected function loadMasters()
    {
        // Breeds
        $this->breeds['Dorper'] = MasterBreed::where('name', 'Dorper')->firstOrFail();
        $this->breeds['Garut'] = MasterBreed::where('name', 'Domba Garut')->firstOrFail();

        // Locations
        $this->locations['Cage A'] = MasterLocation::firstOrCreate(['name' => 'Kandang A (Breeding)', 'type' => 'Kandang Koloni']);
        $this->locations['Cage B'] = MasterLocation::firstOrCreate(['name' => 'Kandang B (Fattening)', 'type' => 'Kandang Koloni']);
        $this->locations['Nursery'] = MasterLocation::firstOrCreate(['name' => 'Kandang Cempe', 'type' => 'Kandang Individu']);

        // Statuses
        $this->statuses['Ready'] = MasterPhysStatus::where('name', 'Dara')->first();
        $this->statuses['Pregnant'] = MasterPhysStatus::where('name', 'Bunting')->first();
        $this->statuses['Lactating'] = MasterPhysStatus::where('name', 'Menyusui')->first();
        $this->statuses['Cempe'] = MasterPhysStatus::where('name', 'Cempe Lahir')->first();
        $this->statuses['Weaned'] = MasterPhysStatus::where('name', 'Cempe Sapih')->first();
        $this->statuses['Fattening'] = MasterPhysStatus::where('name', 'Penggemukan - Siap Jual')->first();

        // Feed
        $this->feedItems['Concentrate'] = InventoryItem::where('name', 'like', '%Konsentrat%')->first();
    }

    protected function createPartners()
    {
        $this->partners['A'] = MasterPartner::create(['name' => 'Investor Alpha (Breeding)', 'contact_info' => '08111111']);
        $this->partners['B'] = MasterPartner::create(['name' => 'Investor Beta (Fattening)', 'contact_info' => '08222222']);
    }

    protected function purchaseInitialStock()
    {
        // Fetch System Owner (First User)
        $owner = User::where('role', 'OWNER')->first() ?? User::first();
        $ownerId = $owner ? $owner->id : null;

        // 1 Sire (Dorper Pure)
        Animal::create([
            'tag_id' => 'SIRE-001',
            'owner_id' => $ownerId,
            'partner_id' => $this->partners['A']->id,
            'category_id' => $this->breeds['Dorper']->category_id,
            'breed_id' => $this->breeds['Dorper']->id,
            'current_location_id' => $this->locations['Cage A']->id,
            'current_phys_status_id' => $this->statuses['Ready']->id,
            'gender' => 'MALE',
            'birth_date' => $this->currentDate->copy()->subYears(2),
            'acquisition_type' => 'BOUGHT',
            'purchase_price' => 15000000,
            'is_active' => true,
            'generation' => 'PURE',
            'ear_tag_color' => 'Blue'
        ]);

        // 10 Dams (Garut for Crossbreeding)
        for ($i = 1; $i <= 10; $i++) {
            Animal::create([
                'tag_id' => 'DAM-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'owner_id' => $ownerId,
                'partner_id' => $this->partners['A']->id,
                'category_id' => $this->breeds['Garut']->category_id,
                'breed_id' => $this->breeds['Garut']->id,
                'current_location_id' => $this->locations['Cage A']->id,
                'current_phys_status_id' => $this->statuses['Ready']->id,
                'gender' => 'FEMALE',
                'birth_date' => $this->currentDate->copy()->subMonths(18),
                'acquisition_type' => 'BOUGHT',
                'purchase_price' => 2500000,
                'is_active' => true,
                'generation' => 'PURE',
                'ear_tag_color' => 'Green'
            ]);
        }

        // Log Purchase of Feed
        InventoryPurchase::create([
            'item_id' => $this->feedItems['Concentrate']->id,
            'date' => $this->currentDate,
            'qty' => 100, // sacks
            'price_total' => 100 * 350000
        ]);
        $this->feedItems['Concentrate']->increment('current_stock', 100);
    }

    protected function runMonthlyEvents()
    {
        $animals = Animal::where('is_active', true)->get();

        foreach ($animals as $animal) {
            // 1. Growth (Weight Log)
            // Sires grow slow, Dams fluctuate, Cempe grows fast
            $growth = 0;
            if ($animal->physStatus->name == 'Cempe Lahir') $growth = rand(300, 500) / 30; // 300-500g/day
            elseif ($animal->physStatus->name == 'Penggemukan - Siap Jual') $growth = rand(150, 300) / 30;
            else $growth = rand(-50, 50) / 30; // Adults stable

            $lastWeight = $animal->weightLogs()->latest('weigh_date')->first()->weight_kg ?? 30;
            $newWeight = $lastWeight + ($growth * 30);

            WeightLog::create([
                'animal_id' => $animal->id,
                'weigh_date' => $this->currentDate,
                'weight_kg' => max(2, $newWeight) // Min 2kg
            ]);

            // Update ADG
            $animal->update(['daily_adg' => $growth]);

            // 2. Lifecycle Checks
            $ageMonths = $animal->birth_date->diffInMonths($this->currentDate);

            // Weaning (3 months)
            if ($animal->current_phys_status_id == $this->statuses['Cempe']->id && $ageMonths >= 3) {
                $animal->update([
                    'current_phys_status_id' => $this->statuses['Weaned']->id,
                    'current_location_id' => $this->locations['Cage B']->id // Move to fattening
                ]);
            }

            // Breeding Logic (Dams)
            if ($animal->gender == 'FEMALE' && $animal->current_phys_status_id == $this->statuses['Ready']->id) {
                // 30% chance to mate this month
                if (rand(1, 100) <= 30) {
                    $sire = Animal::where('gender', 'MALE')->where('is_active', true)->first();
                    if ($sire) {
                        BreedingEvent::create([
                            'dam_id' => $animal->id,
                            'sire_id' => $sire->id,
                            'mating_date' => $this->currentDate,
                            'est_birth_date' => $this->currentDate->copy()->addDays(150),
                            'status' => 'PENDING' // Pregnancy confirmed
                        ]);
                        $animal->update(['current_phys_status_id' => $this->statuses['Pregnant']->id]);
                    }
                }
            }

            // Birthing Logic (Pregnant Dams)
            if ($animal->current_phys_status_id == $this->statuses['Pregnant']->id) {
                $mating = BreedingEvent::where('dam_id', $animal->id)
                    ->where('status', 'PENDING')
                    ->first();

                if ($mating && $mating->est_birth_date->isSameMonth($this->currentDate)) {
                    // GIVE BIRTH
                    $mating->update(['status' => 'SUCCESS']);
                    $animal->update(['current_phys_status_id' => $this->statuses['Lactating']->id]);

                    // Generate 1-2 offspring
                    $count = rand(1, 2);
                    // Fetch System Owner (First User) again just in case, or inherit from Dam
                    $ownerId = $animal->owner_id;

                    for ($k = 0; $k < $count; $k++) {
                        Animal::create([
                            'tag_id' => 'KID-' . Str::random(5),
                            'owner_id' => $ownerId,
                            'partner_id' => $animal->partner_id, // Inherit
                            'dam_id' => $animal->id,
                            'sire_id' => $mating->sire_id,
                            'category_id' => $animal->category_id,
                            'breed_id' => $animal->breed_id, // Simplified inheritance
                            'current_location_id' => $this->locations['Nursery']->id,
                            'current_phys_status_id' => $this->statuses['Cempe']->id,
                            'gender' => rand(0, 1) ? 'MALE' : 'FEMALE',
                            'birth_date' => $this->currentDate,
                            'acquisition_type' => 'BRED',
                            'is_active' => true,
                            'generation' => 'F1',
                            'necklace_color' => 'Yellow'
                        ]);
                    }
                }
            }

            // Lactation End (3 months after birth)
            // Simplified: If Lactating and kid is weaned (3 months later), go back to Ready
            // We need to track last birth... simplified here:
            if ($animal->current_phys_status_id == $this->statuses['Lactating']->id) {
                 // Random check to return to Ready (3-4 months)
                 if (rand(1, 100) <= 25) {
                     $animal->update(['current_phys_status_id' => $this->statuses['Ready']->id]);
                 }
            }
        }

        // 3. Feed Usage (Batch)
        // 1 kg per head per day * 30 days
        $totalHead = $animals->count();
        $usage = $totalHead * 1 * 30; // kg
        // Convert to sacks (50kg/sack)
        $sacks = ceil($usage / 50);

        if ($this->feedItems['Concentrate']->current_stock > $sacks) {
            InventoryUsageLog::create([
                'usage_date' => $this->currentDate,
                'item_id' => $this->feedItems['Concentrate']->id,
                'qty_used' => $sacks,
                'qty_wasted' => 0
            ]);
            $this->feedItems['Concentrate']->decrement('current_stock', $sacks);

            // Distribute Cost (HPP)
            // Simplified: Just add flat cost to all animals
            $cost = $sacks * 350000;
            $costPerHead = $cost / $totalHead;
            Animal::where('is_active', true)->increment('current_hpp', $costPerHead);
        } else {
            // Refill Stock if low
            InventoryPurchase::create([
                'item_id' => $this->feedItems['Concentrate']->id,
                'date' => $this->currentDate,
                'qty' => 50,
                'price_total' => 50 * 350000
            ]);
            $this->feedItems['Concentrate']->increment('current_stock', 50);
        }

        // 4. Sales (Random)
        // Sell Fattening animals > 30kg
        $readyToSell = Animal::where('current_phys_status_id', $this->statuses['Fattening']->id)
            ->where('is_active', true)
            ->get();

        foreach ($readyToSell as $sell) {
            $weight = $sell->weightLogs()->latest('weigh_date')->first()->weight_kg ?? 30;
            if ($weight > 35 && rand(1, 100) <= 20) {
                // Sell
                $price = $weight * 50000; // 50k/kg
                ExitLog::create([
                    'animal_id' => $sell->id,
                    'exit_date' => $this->currentDate,
                    'exit_type' => 'SALE',
                    'price' => $price,
                    'final_hpp' => $sell->current_hpp
                ]);
                $sell->update(['is_active' => false, 'health_status' => 'SOLD']);
            }
        }
    }
}
