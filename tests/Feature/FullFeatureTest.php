<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\MasterPartner;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FullFeatureTest extends TestCase
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
        $this->breed = MasterBreed::create(['name' => 'Dorper', 'category_id' => $this->category->id]);
        $this->location = MasterLocation::create(['name' => 'Cage A', 'type' => 'Colony']);
        $this->status = MasterPhysStatus::create(['name' => 'Healthy']);
        MasterPhysStatus::create(['name' => 'Cempe']);
        MasterPhysStatus::create(['name' => 'Lactating']);

        $this->partner = MasterPartner::create(['name' => 'Mitra A', 'contact_info' => '08123']);
    }

    public function test_update_animal_with_new_fields()
    {
        $animal = Animal::create([
            'tag_id' => 'OLD001',
            'owner_id' => $this->user->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'MALE',
            'birth_date' => Carbon::now(),
            'acquisition_type' => 'BRED',
            'initial_weight' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('animals.update', $animal->id), [
            'tag_id' => 'OLD001',
            'partner_id' => $this->partner->id,
            'necklace_color' => 'Blue',
            'generation' => 'F2',
            'gender' => 'MALE',
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'birth_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('animals.show', $animal->id));

        $animal->refresh();
        $this->assertEquals($this->partner->id, $animal->partner_id);
        $this->assertEquals('Blue', $animal->necklace_color);
        $this->assertEquals('F2', $animal->generation);
        // Observer should have updated ear_tag_color based on F2 Dorper -> Orange
        $this->assertEquals('Orange', $animal->ear_tag_color);
    }
}
