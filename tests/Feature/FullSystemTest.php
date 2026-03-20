<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\MasterPartner;
use App\Models\BreedingEvent;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use App\Models\InventoryUsageLog;
use App\Models\ExitLog;
use App\Models\WeightLog;
use App\Actions\Finance\CalculateDailyHpp;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FullSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;
    protected $breed;
    protected $location;
    protected $status;
    protected $partner;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup common data
        $this->user = User::factory()->create(['role' => 'PEMILIK']);
        $this->category = MasterCategory::create(['name' => 'Sheep']);
        $this->breed = MasterBreed::create([
            'name' => 'Dorper',
            'category_id' => $this->category->id,
            'min_weight_mate' => 20,
            'min_age_mate_months' => 8
        ]);
        $this->location = MasterLocation::create(['name' => 'Cage A', 'type' => 'Colony']);
        $this->status = MasterPhysStatus::create(['name' => 'Sehat']);
        MasterPhysStatus::create(['name' => 'Cempe']);
        MasterPhysStatus::create(['name' => 'Lactating']);

        $this->partner = MasterPartner::create(['name' => 'Mitra A', 'contact_info' => '08123']);
    }

    public function test_est_birth_date_calculation()
    {
        // Create Eligible Dam
        $dam = Animal::create([
            'tag_id' => 'DAM001',
            'owner_id' => $this->user->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'BETINA',
            'birth_date' => Carbon::now()->subYears(2),
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);

        // Create Weight Log to pass eligibility
        $dam->weightLogs()->create([
            'weigh_date' => Carbon::now(),
            'weight_kg' => 45
        ]);

        // Create Sire
        $sire = Animal::create([
            'tag_id' => 'SIRE001',
            'owner_id' => $this->user->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'JANTAN',
            'birth_date' => Carbon::now()->subYears(2),
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);

        // Mating Date
        $matingDate = Carbon::now();

        $response = $this->actingAs($this->user)->post(route('breeding.store', $dam->id), [
            'sire_id' => $sire->id,
            'mating_date' => $matingDate->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('animals.index'));

        // Verify Event
        $event = BreedingEvent::where('dam_id', $dam->id)->first();
        $this->assertNotNull($event);
        $this->assertNotNull($event->est_birth_date);

        // Check calculation: Mating + 150 days
        $expectedDate = $matingDate->copy()->addDays(150)->format('Y-m-d');
        $this->assertEquals($expectedDate, $event->est_birth_date->format('Y-m-d'));
    }

    public function test_full_livestock_lifecycle()
    {
        $this->actingAs($this->user);

        // 1. Setup Inventory (Feed)
        $feed = InventoryItem::create([
            'name' => 'Pakan Konsentrat',
            'category' => 'FEED',
            'unit' => 'kg',
            'current_stock' => 0
        ]);

        $this->post(route('inventory.purchase.store'), [
            'item_id' => $feed->id,
            'qty' => 100,
            'price_total' => 500000, // Rp 5.000 / kg
            'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
        ]);

        $feed->refresh();
        $this->assertEquals(100, $feed->current_stock);

        // 2. Register Bought Animal
        $animalTag = 'E2E-' . rand(1000, 9999);
        
        // Simulate animal arriving 3 days ago
        $threeDaysAgo = Carbon::now()->subDays(3);
        Carbon::setTestNow($threeDaysAgo);

        $response = $this->post(route('animals.store'), [
            'tag_id' => $animalTag,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'JANTAN',
            'birth_date' => Carbon::now()->subYears(1)->format('Y-m-d'),
            'acquisition_type' => 'BELI',
            'purchase_price' => 1500000,
            'initial_weight' => 25,
            'entry_date' => $threeDaysAgo->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('animals.index'));
        $animal = Animal::where('tag_id', $animalTag)->first();
        $this->assertNotNull($animal);
        $this->assertEquals(1500000, $animal->purchase_price);

        // 3. Log Weight (Growth & ADG)
        // Reset to Today
        Carbon::setTestNow();

        $this->post(route('operator.weight.store', $animal->id), [
            'weight_kg' => 28, // 28 - 25 = 3kg gain over 3 days = 1.0 ADG
        ]);

        $animal->refresh();
        $this->assertEquals(28, $animal->latestWeightLog->weight_kg);
        $this->assertEquals(1.0, $animal->daily_adg);

        // 4. Log Feed Usage & HPP Calculation
        // Use 10kg feed for this location yesterday
        $yesterday = Carbon::yesterday();
        $response = $this->post(route('inventory.usage.store'), [
            'item_id' => $feed->id,
            'location_id' => $this->location->id,
            'qty_used' => 10,
            'qty_wasted' => 0,
            'usage_date' => $yesterday->format('Y-m-d'),
        ]);
        
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('inventory_usage_logs', [
            'item_id' => $feed->id,
            'qty_used' => 10,
        ]);

        // Run HPP Action
        $action = new CalculateDailyHpp();
        $action->execute($yesterday);

        $animal->refresh();
        // Total cost for location = 10kg * 5000 = 50.000
        // If this animal is the only one in location, its cost should increase by 50.000
        $this->assertEquals(50000, $animal->accumulated_feed_cost);
        $this->assertEquals(50000, $animal->current_hpp);

        // 5. Exit Animal (Sale)
        $this->post(route('animals.exit.store', $animal->id), [
            'exit_type' => 'JUAL',
            'exit_date' => Carbon::now()->format('Y-m-d'),
            'price' => 2000000,
        ]);

        $animal->refresh();
        $this->assertFalse($animal->is_active);

        $exitLog = ExitLog::where('animal_id', $animal->id)->first();
        $this->assertNotNull($exitLog);
        $this->assertEquals(2000000, $exitLog->price);
        $this->assertEquals(50000, $exitLog->final_hpp); // Final HPP usually includes accumulated costs
    }
}
