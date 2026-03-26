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

class SimpleAnimalTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_save_health_status_directly()
    {
        $user = User::factory()->create(['role' => 'PEMILIK']);
        $category = MasterCategory::create(['name' => 'Sheep']);
        $breed = MasterBreed::create(['name' => 'Dorper', 'category_id' => $category->id]);
        $location = MasterLocation::create(['name' => 'Cage A', 'type' => 'Colony']);
        $status = MasterPhysStatus::create(['name' => 'Sehat']);

        $animal = Animal::create([
            'tag_id' => 'SIMPLE001',
            'owner_id' => $user->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $status->id,
            'gender' => 'JANTAN',
            'birth_date' => Carbon::now(),
            'acquisition_type' => 'HASIL_TERNAK',
            'health_status' => 'SAKIT',
            'is_active' => true,
        ]);

        $this->assertEquals('SAKIT', $animal->refresh()->health_status);
    }
}
