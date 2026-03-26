<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnimalHealthStatusTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;
    protected $breed;
    protected $location;
    protected $status;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'PEMILIK']);
        $this->category = MasterCategory::create(['name' => 'Sheep']);
        $this->breed = MasterBreed::create(['name' => 'Dorper', 'category_id' => $this->category->id]);
        $this->location = MasterLocation::create(['name' => 'Cage A', 'type' => 'Colony']);
        $this->status = MasterPhysStatus::create(['name' => 'Sehat']);
    }

    public function test_can_create_animal_with_health_status()
    {
        $response = $this->actingAs($this->user)->post(route('animals.store'), [
            'tag_id' => 'NEW001',
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'JANTAN',
            'birth_date' => Carbon::now()->format('Y-m-d'),
            'acquisition_type' => 'BELI',
            'initial_weight' => 20,
            'purchase_price' => 1000000,
            'health_status' => 'SAKIT',
        ]);

        $response->assertRedirect(route('animals.index'));
        $this->assertDatabaseHas('animals', [
            'tag_id' => 'NEW001',
            'health_status' => 'SAKIT',
        ]);
    }

    public function test_can_update_animal_health_status()
    {
        $animal = Animal::create([
            'tag_id' => 'EDIT001',
            'owner_id' => $this->user->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'JANTAN',
            'birth_date' => Carbon::now(),
            'acquisition_type' => 'HASIL_TERNAK',
            'health_status' => 'SEHAT',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('animals.update', $animal->id), [
            'tag_id' => 'EDIT001',
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->status->id,
            'gender' => 'JANTAN',
            'birth_date' => Carbon::now()->format('Y-m-d'),
            'health_status' => 'KARANTINA',
        ]);

        $response->assertRedirect(route('animals.show', $animal->id));
        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'health_status' => 'KARANTINA',
        ]);
    }
}
