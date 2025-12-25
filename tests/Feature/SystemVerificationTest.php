<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\BreedingEvent;
use App\Models\ExitLog;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use App\Models\InventoryUsageLog;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemVerificationTest extends TestCase
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

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Setup Master Data
        $this->category = MasterCategory::create(['name' => 'Domba']);
        $this->breed = MasterBreed::create(['name' => 'Dorper', 'category_id' => $this->category->id]);
        $this->location = MasterLocation::create(['name' => 'Kandang A', 'type' => 'Koloni']);
        $this->status = MasterPhysStatus::create(['name' => 'Cempe Lahir']); // Indonesian Name
        $this->partner = MasterPartner::create(['name' => 'Mitra A']);
    }

    /** @test */
    public function it_sets_entry_date_correctly_on_creation()
    {
        // 1. Bought Animal -> Entry Date = Today (created_at)
        $bought = $this->post(route('animals.store'), [
            'tag_id' => 'BOUGHT-001',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'MALE',
            'birth_date' => '2020-01-01',
            'acquisition_type' => 'BOUGHT',
            'purchase_price' => 1000000,
            'initial_weight' => 30,
        ]);

        $boughtAnimal = Animal::where('tag_id', 'BOUGHT-001')->first();
        $this->assertTrue(Carbon::parse($boughtAnimal->entry_date)->isToday());

        // 2. Bred Animal -> Entry Date = Birth Date
        $bred = $this->post(route('animals.store'), [
            'tag_id' => 'BRED-001',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'FEMALE',
            'birth_date' => '2023-01-01',
            'acquisition_type' => 'BRED',
            'initial_weight' => 3,
        ]);

        $bredAnimal = Animal::where('tag_id', 'BRED-001')->first();
        $this->assertEquals('2023-01-01', $bredAnimal->entry_date->format('Y-m-d'));
        $this->assertEquals(0, $bredAnimal->purchase_price);
    }

    /** @test */
    public function it_calculates_daily_hpp_only_for_present_animals()
    {
        // Create Feed Item
        $feed = InventoryItem::create(['name' => 'Feed A', 'category' => 'FEED', 'unit' => 'kg', 'current_stock' => 100]);
        InventoryPurchase::create(['item_id' => $feed->id, 'qty' => 100, 'price_total' => 100000, 'purchase_date' => now()]); // price = 1000

        // Create Animal Arrived Today
        $animalToday = Animal::create([
            'tag_id' => 'TODAY',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'MALE',
            'birth_date' => '2020-01-01',
            'entry_date' => Carbon::today(),
            'is_active' => true,
            'owner_id' => $this->user->id,
        ]);

        // Create Animal Arrived Yesterday
        $animalYesterday = Animal::create([
            'tag_id' => 'YESTERDAY',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'MALE',
            'birth_date' => '2020-01-01',
            'entry_date' => Carbon::yesterday(),
            'is_active' => true,
            'owner_id' => $this->user->id,
        ]);

        // Create Usage Log for YESTERDAY
        InventoryUsageLog::create([
            'item_id' => $feed->id,
            'location_id' => $this->location->id,
            'qty_used' => 10, // Total cost = 10 * 1000 = 10,000
            'usage_date' => Carbon::yesterday(),
            'user_id' => $this->user->id,
        ]);

        // Run Calculation for YESTERDAY
        $action = new \App\Actions\Finance\CalculateDailyHpp();
        $action->execute(Carbon::yesterday());

        // Assert:
        // Total Cost 10,000.
        // AnimalYesterday was present. AnimalToday was NOT present yesterday.
        // So Cost should be split by 1 (AnimalYesterday).
        // AnimalYesterday cost += 10,000.
        // AnimalToday cost += 0.

        $animalYesterday->refresh();
        $animalToday->refresh();

        $this->assertEquals(10000, $animalYesterday->accumulated_feed_cost);
        $this->assertEquals(0, $animalToday->accumulated_feed_cost);
    }

    /** @test */
    public function it_prevents_repeated_exit()
    {
        $animal = Animal::create([
            'tag_id' => 'EXIT-001',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'MALE',
            'birth_date' => '2020-01-01',
            'is_active' => false, // Already exited
            'owner_id' => $this->user->id,
        ]);

        $response = $this->post(route('animals.exit.store', $animal->id), [
            'exit_type' => 'DEATH',
            'exit_date' => now(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Animal is already exited.');
    }

    /** @test */
    public function it_smartly_completes_breeding_on_separation()
    {
        $locA = $this->location;
        $locB = MasterLocation::create(['name' => 'Kandang B', 'type' => 'Individu']);

        $sire = Animal::create([
            'tag_id' => 'SIRE',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $locA->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'MALE',
            'birth_date' => '2020-01-01',
            'owner_id' => $this->user->id,
        ]);

        $dam = Animal::create([
            'tag_id' => 'DAM',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $locA->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'FEMALE',
            'birth_date' => '2020-01-01',
            'owner_id' => $this->user->id,
        ]);

        $breeding = BreedingEvent::create([
            'sire_id' => $sire->id,
            'dam_id' => $dam->id,
            'mating_date' => now(),
            'status' => 'PENDING'
        ]);

        // Move Sire to Location B
        $sire->update(['current_location_id' => $locB->id]);

        $breeding->refresh();
        $this->assertEquals('COMPLETED', $breeding->status);
    }
}
