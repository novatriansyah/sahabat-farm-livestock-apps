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

class PartnerAndBirthTest extends TestCase
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
        MasterPhysStatus::create(['name' => 'Cempe']); // Required for birth logic
        MasterPhysStatus::create(['name' => 'Lactating']); // Required for dam update

        $this->partner = MasterPartner::create(['name' => 'Mitra A', 'contact_info' => '08123']);
    }

    public function test_can_create_partner()
    {
        $response = $this->actingAs($this->user)->post(route('partners.store'), [
            'name' => 'Mitra B',
            'contact_info' => 'Test Contact'
        ]);

        $response->assertRedirect(route('partners.index'));
        $this->assertDatabaseHas('master_partners', ['name' => 'Mitra B']);
    }

    public function test_can_register_birth_with_genetics()
    {
        // 1. Create Dam (Mother) owned by Partner A
        $dam = Animal::create([
            'tag_id' => 'DAM001',
            'owner_id' => $this->user->id,
            'partner_id' => $this->partner->id,
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

        // 2. Register Birth
        $response = $this->actingAs($this->user)->post(route('birth.store'), [
            'tag_id' => 'KID001',
            'dam_id' => $dam->id,
            'birth_date' => Carbon::now()->format('Y-m-d'),
            'gender' => 'MALE',
            'initial_weight' => 3.5,
            'breed_id' => $this->breed->id,
            'category_id' => $this->category->id,
            'current_location_id' => $this->location->id,
            'generation' => 'F1', // Logic: F1 Dorper should trigger Kuning Ear Tag
            'necklace_color' => 'Red',
        ]);

        $response->assertRedirect(route('animals.index'));

        // 3. Verify Offspring
        $offspring = Animal::where('tag_id', 'KID001')->first();
        $this->assertNotNull($offspring);
        $this->assertEquals($dam->id, $offspring->dam_id);
        $this->assertEquals($this->partner->id, $offspring->partner_id); // Inherited ownership
        $this->assertEquals('Kuning', $offspring->ear_tag_color); // Logic verification
        $this->assertEquals('Red', $offspring->necklace_color);
        $this->assertEquals('F1', $offspring->generation);
    }
}
