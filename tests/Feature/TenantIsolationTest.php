<?php

namespace Tests\Feature;

use App\Exports\ImportCompatibleAnimalExport;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use DatabaseTransactions;

    private MasterPartner $partnerA;
    private MasterPartner $partnerB;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create(['role' => 'PEMILIK']);
        $this->partnerA = MasterPartner::create(['name' => 'Mitra A', 'contact_info' => 'partnerA@test.com']);
        $this->partnerB = MasterPartner::create(['name' => 'Mitra B', 'contact_info' => 'partnerB@test.com']);

        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::create(['name' => 'Garut', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang A', 'type' => 'Koloni']);
        $statusSehat = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        // Partner A animal
        Animal::create([
            'id' => '00000000-0000-0000-0000-000000000001',
            'tag_id' => 'TAG-A1',
            'owner_id' => $owner->id,
            'partner_id' => $this->partnerA->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);

        // Partner B animal
        Animal::create([
            'id' => '00000000-0000-0000-0000-000000000002',
            'tag_id' => 'TAG-B1',
            'owner_id' => $owner->id,
            'partner_id' => $this->partnerB->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);
    }

    public function test_partner_a_export_has_zero_partner_b_data(): void
    {
        $exportA = new ImportCompatibleAnimalExport((string) $this->partnerA->id);
        $rowsA = $exportA->sheets()['DATA_TERNAK']->collection();

        $this->assertCount(1, $rowsA);
        $this->assertEquals('TAG-A1', $rowsA->first()['tag_id']);
        $this->assertNull($rowsA->firstWhere('tag_id', 'TAG-B1'));
    }

    public function test_mitra_role_cannot_download_other_partner(): void
    {
        $userMitraA = User::factory()->create([
            'role' => 'MITRA',
            'partner_id' => $this->partnerA->id,
        ]);

        // Attempting to export Partner B as Partner A user should be blocked (302/403)
        $response = $this->actingAs($userMitraA)
            ->get('/admin/export/animals/import-compatible?partner_id=' . $this->partnerB->id);

        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_invalid_partner_id_rejected(): void
    {
        $owner = User::factory()->create(['role' => 'PEMILIK']);

        $response = $this->actingAs($owner)
            ->get('/admin/export/animals/import-compatible?partner_id=999999');

        $response->assertSessionHasErrors('partner_id');
    }
}
