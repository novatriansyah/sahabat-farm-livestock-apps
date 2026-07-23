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
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SimpleAnimalTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_update_animal_with_new_fields()
    {
        $user = User::factory()->create(['role' => 'PEMILIK']);
        $category = MasterCategory::create(['name' => 'Sheep']);
        $breed = MasterBreed::create(['name' => 'Dorper', 'category_id' => $category->id]);
        $location = MasterLocation::create(['name' => 'Cage A', 'type' => 'Colony']);
        $status = MasterPhysStatus::create(['name' => 'Sehat']);
        MasterPhysStatus::create(['name' => 'Cempe']);
        MasterPhysStatus::create(['name' => 'Lactating']);
        $partner = MasterPartner::create(['name' => 'Mitra A', 'contact_info' => '08123']);

        $animal = Animal::create([
            'tag_id' => 'OLD001',
            'owner_id' => $user->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $status->id,
            'gender' => 'JANTAN',
            'generation' => 'F2',
            'ear_tag_color' => 'Orange',
            'birth_date' => Carbon::now(),
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('animals.update', $animal->id), [
            'tag_id' => 'OLD001',
            'partner_id' => $partner->id,
            'necklace_color' => 'Blue',
            'ear_tag_color' => 'Red',
            'generation' => 'F2',
            'gender' => 'JANTAN',
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $status->id,
            'health_status' => 'SEHAT',
            'birth_date' => Carbon::now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('animals.show', $animal->id));

        $animal->refresh();
        $this->assertEquals($partner->id, $animal->partner_id);
        $this->assertEquals('Blue', $animal->necklace_color);
        $this->assertEquals('F2', $animal->generation);
        $this->assertEquals('Red', $animal->ear_tag_color);
    }
}
