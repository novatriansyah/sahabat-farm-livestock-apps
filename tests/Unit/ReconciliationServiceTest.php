<?php

namespace Tests\Unit;

use App\Models\Animal;
use App\Models\AnimalEarTagLog;
use App\Models\ReconciliationLog;
use App\Models\User;
use App\Services\ReconciliationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReconciliationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReconciliationService $service;
    private string $ownerId;
    private int $catId;
    private int $breedId;
    private int $locId;
    private int $physId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReconciliationService();

        $catId = \DB::table('master_categories')->insertGetId(['name' => 'Test', 'created_at' => now(), 'updated_at' => now()]);
        $breedId = \DB::table('master_breeds')->insertGetId(['category_id' => $catId, 'name' => 'Dorper', 'created_at' => now(), 'updated_at' => now()]);
        $locId = \DB::table('master_locations')->insertGetId(['name' => 'Kandang A', 'type' => 'Koloni', 'created_at' => now(), 'updated_at' => now()]);
        $physId = \DB::table('master_phys_statuses')->insertGetId(['name' => 'SEHAT', 'created_at' => now(), 'updated_at' => now()]);

        $user = User::factory()->create(['role' => 'PEMILIK']);
        $this->ownerId = $user->id;

        $this->catId = $catId;
        $this->breedId = $breedId;
        $this->locId = $locId;
        $this->physId = $physId;
    }

    private function createAnimal(array $overrides = []): Animal
    {
        return Animal::create(array_merge([
            'id' => (string) Str::uuid(),
            'tag_id' => 'TEST-001',
            'owner_id' => $this->ownerId,
            'category_id' => $this->catId,
            'breed_id' => $this->breedId,
            'current_location_id' => $this->locId,
            'current_phys_status_id' => $this->physId,
            'gender' => 'BETINA',
            'birth_date' => '2025-06-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
            'health_status' => 'SEHAT',
            'entry_date' => '2025-01-01',
            'generation' => 'F1',
            'ear_tag_color' => 'Kuning',
            'necklace_color' => null,
            'purchase_price' => 0,
            'sale_price' => 0,
            'partner_id' => null,
            'google_drive_link' => null,
        ], $overrides));
    }

    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'tag_id' => 'B31',
            'gender' => 'BETINA',
            'is_active' => true,
            'birth_date' => '2025-06-01',
            'generation' => 'F1',
            'ear_tag_color' => 'Kuning',
            'necklace_color' => null,
            'purchase_price' => 0,
            'sale_price' => 0,
            'partner_id' => null,
            'current_location_id' => null,
            'breed_id' => null,
            'google_drive_link' => null,
        ], $overrides);
    }

    public function test_compare_returns_same_for_identical_data(): void
    {
        $animal = $this->createAnimal(['tag_id' => 'B31']);
        $uploaded = collect([$this->makeRow(['id' => $animal->id, 'tag_id' => 'B31'])]);

        $result = $this->service->reconcileData($uploaded);

        $this->assertArrayHasKey('batch_id', $result);
        $this->assertArrayHasKey('summary', $result);
        $this->assertEquals(1, $result['summary']['SAME']);
        $this->assertEquals(0, $result['summary']['CONFLICT']);
    }

    public function test_compare_returns_conflict_for_differing_data(): void
    {
        $animal = $this->createAnimal(['tag_id' => 'B31', 'gender' => 'BETINA']);
        $uploaded = collect([$this->makeRow(['id' => $animal->id, 'tag_id' => 'B31', 'gender' => 'JANTAN'])]);

        $result = $this->service->reconcileData($uploaded);

        $this->assertEquals(1, $result['summary']['CONFLICT']);
    }

    public function test_compare_returns_excel_only_for_new_tag(): void
    {
        $uploaded = collect([$this->makeRow(['tag_id' => 'NEW-001'])]);

        $result = $this->service->reconcileData($uploaded);

        $this->assertEquals(1, $result['summary']['EXCEL_ONLY']);
    }

    public function test_compare_returns_web_only_for_animals_not_in_upload(): void
    {
        $this->createAnimal(['tag_id' => 'B31']);
        $this->createAnimal(['tag_id' => 'B32']);

        $uploaded = collect([$this->makeRow(['tag_id' => 'B31'])]);

        $result = $this->service->reconcileData($uploaded);

        $this->assertGreaterThanOrEqual(1, $result['summary']['WEB_ONLY']);
    }

    public function test_compare_matches_by_uuid_when_tag_id_changes(): void
    {
        $animal = $this->createAnimal(['tag_id' => 'OLD-001']);
        $uploaded = collect([$this->makeRow(['id' => $animal->id, 'tag_id' => 'NEW-001'])]);

        $result = $this->service->reconcileData($uploaded);

        $this->assertEquals(0, $result['summary']['EXCEL_ONLY']);
        $this->assertEquals(1, $result['summary']['CONFLICT']);
    }

    public function test_compare_matches_by_tag_history(): void
    {
        $animal = $this->createAnimal(['tag_id' => 'CURRENT-001']);

        AnimalEarTagLog::create([
            'animal_id' => $animal->id,
            'old_tag_id' => 'OLD-001',
            'new_tag_id' => 'CURRENT-001',
            'changed_at' => now(),
        ]);

        $uploaded = collect([$this->makeRow(['tag_id' => 'OLD-001'])]);

        $result = $this->service->reconcileData($uploaded);

        $this->assertEquals(0, $result['summary']['EXCEL_ONLY']);
    }

    public function test_batch_id_is_unique(): void
    {
        $result1 = $this->service->reconcileData(collect([]));
        $result2 = $this->service->reconcileData(collect([]));

        $this->assertNotEquals($result1['batch_id'], $result2['batch_id']);
    }

    public function test_reconciliation_is_read_only_and_zero_write(): void
    {
        $animal = $this->createAnimal(['tag_id' => 'B31']);
        $uploaded = collect([$this->makeRow(['id' => $animal->id, 'tag_id' => 'B31'])]);

        $initialCount = ReconciliationLog::count();
        $this->service->reconcileData($uploaded);

        $this->assertEquals($initialCount, ReconciliationLog::count());
    }
}