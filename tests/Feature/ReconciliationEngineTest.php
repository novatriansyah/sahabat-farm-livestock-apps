<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\ReconciliationLog;
use App\Models\User;
use App\Services\ReconciliationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReconciliationEngineTest extends TestCase
{
    use DatabaseTransactions;

    protected User $owner;
    protected MasterCategory $category;
    protected MasterBreed $breed;
    protected MasterLocation $location;
    protected MasterPhysStatus $statusSehat;

    protected function setUp(): void
    {
        parent::setUp();

        Animal::whereIn('tag_id', ['036', '010', '099'])->forceDelete();

        $this->owner = User::factory()->create(['role' => 'PEMILIK']);
        $this->category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $this->breed = MasterBreed::create(['name' => 'DORPER', 'category_id' => $this->category->id]);
        $this->location = MasterLocation::firstOrCreate(['name' => 'Kandang A', 'type' => 'Koloni']);
        $this->statusSehat = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        // DB Animal 1: Tag 036
        Animal::create([
            'id' => '11111111-1111-1111-1111-111111111111',
            'tag_id' => '036',
            'owner_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
            'purchase_price' => 5000000,
        ]);

        // DB Animal 2: Tag 010 (will have conflict in gender)
        Animal::create([
            'id' => '22222222-2222-2222-2222-222222222222',
            'tag_id' => '010',
            'owner_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
            'purchase_price' => 3000000,
        ]);

        // DB Animal 3: Tag 099 (will not be in Excel upload -> WEB_ONLY)
        Animal::create([
            'id' => '33333333-3333-3333-3333-333333333333',
            'tag_id' => '099',
            'owner_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);
    }

    public function test_reconciliation_math_invariant_and_zero_db_write(): void
    {
        $uploadedRows = collect([
            // Row 1: Matching Animal 1 -> SAME
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'tag_id' => '036',
                'gender' => 'BETINA',
                'is_active' => true,
            ],
            // Row 2: Matching Animal 2 -> CONFLICT (gender conflict)
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'tag_id' => '010',
                'gender' => 'BETINA', // Conflict! DB has JANTAN
                'is_active' => true,
            ],
            // Row 3: New animal -> EXCEL_ONLY
            [
                'tag_id' => 'NEW-999',
                'gender' => 'JANTAN',
            ],
        ]);

        $initialLogCount = ReconciliationLog::count();
        $initialAnimalCount = Animal::count();

        $service = new ReconciliationService();
        $output = $service->reconcileData($uploadedRows);

        $this->assertEquals($initialLogCount, ReconciliationLog::count());
        $this->assertEquals($initialAnimalCount, Animal::count());

        $summary = $output['summary'];
        $sum = $summary['SAME'] + $summary['WEB_ONLY'] + $summary['EXCEL_ONLY'] + $summary['CONFLICT'] + $summary['UNCERTAIN'];

        $this->assertEquals(1, $summary['SAME']);
        $this->assertEquals(1, $summary['CONFLICT']);
        $this->assertEquals(1, $summary['EXCEL_ONLY']);
        $this->assertEquals(1, $summary['WEB_ONLY']);
        $this->assertEquals(0, $summary['UNCERTAIN']);
        $this->assertEquals(4, $sum);
        $this->assertEquals($summary['TOTAL_UNION'], $sum);
    }

    public function test_reconciliation_matching_ladder_and_uncertain_resolution(): void
    {
        AnimalEarTagLog::create([
            'animal_id' => '11111111-1111-1111-1111-111111111111',
            'old_tag_id' => 'HISTORY-777',
            'new_tag_id' => '036',
            'reason' => 'Tag damage',
            'changed_at' => now(),
        ]);

        $uploadedRows = collect([
            [
                'tag_id' => 'HISTORY-777',
                'gender' => 'BETINA',
            ],
        ]);

        $service = new ReconciliationService();
        $output = $service->reconcileData($uploadedRows);

        $results = collect($output['results']);
        $historyMatch = $results->firstWhere('tag_id', '036');

        $this->assertNotNull($historyMatch);
        $this->assertEquals('Tag History', $historyMatch['match_tier']);
    }

    public function test_duplicate_active_tag_triggers_uncertain_status(): void
    {
        // Two DB animals matching same composite criteria (birth date + gender) without tag or ID in excel row
        Animal::create([
            'id' => '44444444-4444-4444-4444-444444444444',
            'tag_id' => '055',
            'owner_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-06-06',
            'acquisition_type' => 'HASIL_TERNAK',
        ]);

        Animal::create([
            'id' => '55555555-5555-5555-5555-555555555555',
            'tag_id' => '066',
            'owner_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'breed_id' => $this->breed->id,
            'current_location_id' => $this->location->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-06-06',
            'acquisition_type' => 'HASIL_TERNAK',
        ]);

        $uploadedRows = collect([
            [
                'birth_date' => '2025-06-06',
                'gender' => 'BETINA',
            ],
        ]);

        $service = new ReconciliationService();
        $output = $service->reconcileData($uploadedRows);

        $summary = $output['summary'];
        $this->assertGreaterThanOrEqual(1, $summary['UNCERTAIN']);
    }
}
