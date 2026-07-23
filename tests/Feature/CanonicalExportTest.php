<?php

namespace Tests\Feature;

use App\Exports\AnimalMasterExport;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['role' => 'PEMILIK']);
        $partnerA = MasterPartner::create(['name' => 'Mitra Berkah']);
        $partnerB = MasterPartner::create(['name' => 'Mitra Sukses']);

        $category = MasterCategory::create(['name' => 'Kambing']);
        $breed = MasterBreed::create(['name' => 'Garut', 'category_id' => $category->id]);
        $location = MasterLocation::create(['name' => 'Kandang A', 'type' => 'Koloni']);

        $statusSehat = MasterPhysStatus::create(['name' => 'SEHAT']);
        $statusDead = MasterPhysStatus::create(['name' => 'DEAD']);

        Animal::create([
            'tag_id'                 => '036',
            'owner_id'               => $user->id,
            'partner_id'             => $partnerA->id,
            'category_id'            => $category->id,
            'breed_id'               => $breed->id,
            'current_location_id'    => $location->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender'                 => 'BETINA',
            'birth_date'             => '2023-01-15',
            'entry_date'             => '2023-02-01',
            'acquisition_type'       => 'BELI',
            'is_active'              => true,
        ]);

        Animal::create([
            'tag_id'                 => '099',
            'owner_id'               => $user->id,
            'partner_id'             => $partnerB->id,
            'category_id'            => $category->id,
            'breed_id'               => $breed->id,
            'current_location_id'    => $location->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender'                 => 'JANTAN',
            'birth_date'             => '2023-01-15',
            'entry_date'             => '2023-02-01',
            'acquisition_type'       => 'BELI',
            'is_active'              => true,
        ]);

        Animal::create([
            'tag_id'                 => 'B43',
            'owner_id'               => $user->id,
            'partner_id'             => null,
            'category_id'            => $category->id,
            'breed_id'               => $breed->id,
            'current_location_id'    => $location->id,
            'current_phys_status_id' => $statusDead->id,
            'gender'                 => 'BETINA',
            'birth_date'             => '2023-01-15',
            'entry_date'             => '2023-02-01',
            'acquisition_type'       => 'HASIL_TERNAK',
            'is_active'              => false,
        ]);
    }

    public function test_canonical_export_contains_all_13_sheets_and_unfiltered_records(): void
    {
        $export = new AnimalMasterExport();
        $sheets = $export->sheets();

        $this->assertCount(13, $sheets);
        $this->assertArrayHasKey('MANIFEST', $sheets);
        $this->assertArrayHasKey('ANIMALS_CURRENT', $sheets);

        $animalsSheet = $sheets['ANIMALS_CURRENT'];
        $queryCount = $animalsSheet->query()->count();
        $totalDbCount = Animal::count();

        $this->assertEquals($totalDbCount, $queryCount);
        $this->assertEquals(3, $queryCount);
    }

    public function test_canonical_export_preserves_b43_dead_status_and_leading_zero_tags(): void
    {
        $export = new AnimalMasterExport();
        $animalsSheet = $export->sheets()['ANIMALS_CURRENT'];
        $rows = $animalsSheet->query()->get();

        $b43 = $rows->firstWhere('tag_id', 'B43');
        $this->assertNotNull($b43);
        $this->assertFalse((bool) $b43->is_active);
        $this->assertEquals('DEAD', $b43->physStatus->name);

        $tag036 = $rows->firstWhere('tag_id', '036');
        $this->assertNotNull($tag036);
        $mappedRow = $animalsSheet->map($tag036);
        $this->assertEquals('036', $mappedRow[1]);
    }
}
