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
        $this->user = User::factory()->create(['role' => 'OWNER']);
        $this->category = MasterCategory::create(['name' => 'Sheep']);
        $this->breed = MasterBreed::create([
            'name' => 'Dorper',
            'category_id' => $this->category->id,
            'min_weight_mate' => 20,
            'min_age_mate_months' => 8
        ]);
        $this->location = MasterLocation::create(['name' => 'Cage A', 'type' => 'Colony']);
        $this->status = MasterPhysStatus::create(['name' => 'Healthy']);
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
            'gender' => 'FEMALE',
            'birth_date' => Carbon::now()->subYears(2),
            'acquisition_type' => 'BRED',
            'initial_weight' => 40,
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
            'gender' => 'MALE',
            'birth_date' => Carbon::now()->subYears(2),
            'acquisition_type' => 'BRED',
            'initial_weight' => 60,
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
}
