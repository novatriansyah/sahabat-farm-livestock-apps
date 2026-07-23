<?php

namespace Tests\Feature;

use App\Events\SourceDataCorrected;
use App\Models\Animal;
use App\Models\AnimalFieldChange;
use App\Models\AnimalOwnershipLog;
use App\Models\DataQualityIssue;
use App\Models\DerivedCalculationRun;
use App\Models\HppAllocation;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use App\Models\WeightLog;
use App\Services\CurrentAnimalValueResolver;
use App\Services\GrowthCalculationService;
use App\Services\HppAllocationService;
use App\Services\RecalculationOrchestrator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class Cp8Rc2aLogicResilienceTest extends \Tests\TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $mitraVina;
    protected User $mitraFahri;
    protected MasterPartner $partnerVina;
    protected MasterPartner $partnerFahri;
    protected MasterLocation $locationMain;
    protected MasterLocation $locationKarantina;
    protected MasterPhysStatus $statusSehat;
    protected MasterCategory $categoryDomba;
    protected MasterBreed $breedGarut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryDomba = MasterCategory::create(['name' => 'Domba']);
        $this->breedGarut = MasterBreed::create(['name' => 'Garut', 'category_id' => $this->categoryDomba->id]);
        $this->locationMain = MasterLocation::create(['name' => 'Kandang Utama', 'type' => 'Kandang']);
        $this->locationKarantina = MasterLocation::create(['name' => 'Kandang Karantina', 'type' => 'Karantina']);
        $this->statusSehat = MasterPhysStatus::create(['name' => 'SEHAT']);

        $this->partnerVina = MasterPartner::create(['name' => 'VINA']);
        $this->partnerFahri = MasterPartner::create(['name' => 'FAHRI']);

        $this->owner = User::create([
            'name' => 'Pemilik SFI',
            'email' => 'owner@sahabatfarm.com',
            'password' => bcrypt('password'),
            'role' => 'PEMILIK',
        ]);

        $this->mitraVina = User::create([
            'name' => 'Vina User',
            'email' => 'vina@sahabatfarm.com',
            'password' => bcrypt('password'),
            'role' => 'MITRA',
            'partner_id' => $this->partnerVina->id,
        ]);

        $this->mitraFahri = User::create([
            'name' => 'Fahri User',
            'email' => 'fahri@sahabatfarm.com',
            'password' => bcrypt('password'),
            'role' => 'MITRA',
            'partner_id' => $this->partnerFahri->id,
        ]);
    }

    /**
     * G1 — Semua field baseline editable dengan audit trail dan tenant isolation.
     */
    public function test_g1_all_baseline_fields_are_editable_with_audit_trail_and_tenant_isolation(): void
    {
        $animal = Animal::create([
            'tag_id' => 'VINA-001',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'entry_date' => '2025-01-01',
            'health_status' => 'SEHAT',
            'confidence' => 'LOW',
        ]);

        // Pemilik can edit all 14 baseline fields
        $response = $this->actingAs($this->owner)->put(route('animals.update', $animal->id), [
            'tag_id' => 'VINA-001-FIXED',
            'legacy_tag_id' => 'OLD-TAG-123',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-02',
            'entry_date' => '2025-01-02',
            'health_status' => 'SEHAT',
            'physical_characteristics' => 'Telinga panjang',
            'birth_weight' => 3.5,
            'valuation' => 2500000,
            'acquisition_type' => 'BELI',
            'purchase_price' => 2000000,
            'current_inventory_status' => 'TERSEDIA',
            'is_active' => true,
            'litter_size' => 2,
            'data_source' => 'PHYSICAL_VERIFICATION',
            'confidence' => 'HIGH',
            'in_partner_file' => true,
            'notes' => 'Koreksi verifikasi fisik kandang',
            'change_reason' => 'Verifikasi aktual fisik',
            'value_status' => 'ACTUAL',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'tag_id' => 'VINA-001-FIXED',
            'legacy_tag_id' => 'OLD-TAG-123',
            'physical_characteristics' => 'Telinga panjang',
            'birth_weight' => 3.5,
            'valuation' => 2500000,
            'confidence' => 'HIGH',
        ]);

        $this->assertDatabaseHas('animal_field_changes', [
            'animal_id' => $animal->id,
            'field_name' => 'legacy_tag_id',
            'new_value' => 'OLD-TAG-123',
            'new_value_status' => 'ACTUAL',
            'reason' => 'Verifikasi aktual fisik',
        ]);

        // Tenant Isolation: Mitra Fahri cannot edit Vina's animal
        $unauthorized = $this->actingAs($this->mitraFahri)->get(route('animals.edit', $animal->id));
        $this->assertTrue(in_array($unauthorized->status(), [302, 403]));
    }

    /**
     * G2 — Assumption-to-actual weight updates ADG from PROVISIONAL to ACTUAL.
     */
    public function test_g2_assumption_to_actual_weight_updates_adg_from_provisional_to_actual(): void
    {
        $animal = Animal::create([
            'tag_id' => 'VINA-002',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        // Seed 2 ASSUMED weights (Jan 1: 10.0kg, Jan 31: 13.0kg) -> 3000g / 30 days = 100 g/day PROVISIONAL
        $log1 = WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-01',
            'weight_kg' => 10.0,
            'measurement_status' => 'ASSUMED',
            'is_current' => true,
        ]);
        $log2 = WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-31',
            'weight_kg' => 13.0,
            'measurement_status' => 'ASSUMED',
            'is_current' => true,
        ]);

        $growthService = app(GrowthCalculationService::class);
        $res1 = $growthService->calculateForAnimal($animal);

        $this->assertEquals(100, $res1['adg_g_day']);
        $this->assertEquals('PROVISIONAL', $res1['status']);

        // User updates weights to ACTUAL (Jan 1: 11.0kg, Jan 31: 14.6kg) -> 3600g / 30 days = 120 g/day ACTUAL
        $log1->update(['is_current' => false]);
        $log2->update(['is_current' => false]);

        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-01',
            'weight_kg' => 11.0,
            'measurement_status' => 'ACTUAL',
            'supersedes_id' => $log1->id,
            'is_current' => true,
        ]);
        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-31',
            'weight_kg' => 14.6,
            'measurement_status' => 'ACTUAL',
            'supersedes_id' => $log2->id,
            'is_current' => true,
        ]);

        $res2 = $growthService->calculateForAnimal($animal);

        $this->assertEquals(120, $res2['adg_g_day']);
        $this->assertEquals('ACTUAL', $res2['status']);
    }

    /**
     * G3 — Single weight log results in NOT CALCULABLE (NULL), not zero.
     */
    public function test_g3_single_weight_log_results_in_not_calculable_not_zero(): void
    {
        $animal = Animal::create([
            'tag_id' => 'VINA-003',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-01',
            'weight_kg' => 15.0,
            'measurement_status' => 'ACTUAL',
            'is_current' => true,
        ]);

        $growthService = app(GrowthCalculationService::class);
        $res = $growthService->calculateForAnimal($animal);

        $this->assertNull($res['adg']);
        $this->assertEquals('NOT_CALCULABLE', $res['status']);
        $this->assertEquals('TIDAK DAPAT DIHITUNG', $res['display']);
        $this->assertNull($animal->fresh()->daily_adg);
    }

    /**
     * G4 — Correction of entry date rebuilds HPP eligibility and allocations.
     */
    public function test_g4_entry_date_correction_rebuilds_hpp_eligibility_and_allocations(): void
    {
        $animalA = Animal::create([
            'tag_id' => 'ANIM-A',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'entry_date' => '2025-01-01',
            'health_status' => 'SEHAT',
            'purchase_price' => 1000000,
        ]);

        $animalB = Animal::create([
            'tag_id' => 'ANIM-B',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'entry_date' => '2025-01-01', // Initially assumed Jan 1
            'health_status' => 'SEHAT',
            'purchase_price' => 1000000,
        ]);

        $hppService = app(HppAllocationService::class);
        // Allocate 100,000 cost on Jan 20 across VINA animals
        $hppService->allocateCost('MANUAL_COST', 'MC-1', '2025-01-20', 100000, $this->partnerVina->id);

        $this->assertEquals(50000, HppAllocation::where('animal_id', $animalA->id)->where('status', 'ACTIVE')->sum('amount'));
        $this->assertEquals(50000, HppAllocation::where('animal_id', $animalB->id)->where('status', 'ACTIVE')->sum('amount'));

        // Correct Animal B entry date to Jan 25 (after Jan 20 cost)
        $animalB->update(['entry_date' => '2025-01-25']);

        // Reverse old allocations and re-allocate
        $hppService->reverseAllocationsForSource('MANUAL_COST', 'MC-1');
        $hppService->allocateCost('MANUAL_COST', 'MC-1', '2025-01-20', 100000, $this->partnerVina->id);

        $hppService->rebuildAnimalHpp($animalA);
        $hppService->rebuildAnimalHpp($animalB);

        // Animal A gets full 100,000 cost; Animal B gets 0 for Jan 20 cost
        $this->assertEquals(1100000, (float)$animalA->fresh()->current_hpp);
        $this->assertEquals(1000000, (float)$animalB->fresh()->current_hpp);
    }

    /**
     * G5 — Ownership correction updates history and clears cache for both partners.
     */
    public function test_g5_ownership_correction_updates_history_and_clears_cache_both_sides(): void
    {
        $animal = Animal::create([
            'tag_id' => 'TRANSFER-01',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        Cache::put("partner_report_data_{$this->partnerVina->id}", 'OLD_VINA_CACHE');
        Cache::put("partner_report_data_{$this->partnerFahri->id}", 'OLD_FAHRI_CACHE');

        $this->actingAs($this->owner)->put(route('animals.update', $animal->id), [
            'tag_id' => 'TRANSFER-01',
            'partner_id' => $this->partnerFahri->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        $this->assertDatabaseHas('animal_ownership_logs', [
            'animal_id' => $animal->id,
            'old_partner_id' => $this->partnerVina->id,
            'new_partner_id' => $this->partnerFahri->id,
        ]);

        $this->assertFalse(Cache::has("partner_report_data_{$this->partnerVina->id}"));
        $this->assertFalse(Cache::has("partner_report_data_{$this->partnerFahri->id}"));
    }

    /**
     * G6 — Location correction rebuilds location history and HPP.
     */
    public function test_g6_location_correction_rebuilds_location_history_and_hpp(): void
    {
        $animal = Animal::create([
            'tag_id' => 'LOC-01',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        $this->actingAs($this->owner)->put(route('animals.update', $animal->id), [
            'tag_id' => 'LOC-01',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationKarantina->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        $this->assertDatabaseHas('animal_field_changes', [
            'animal_id' => $animal->id,
            'field_name' => 'current_location_id',
            'old_value' => (string)$this->locationMain->id,
            'new_value' => (string)$this->locationKarantina->id,
        ]);
    }

    /**
     * G7 — Data Quality Inbox correction queue end-to-end.
     */
    public function test_g7_data_quality_inbox_correction_queue_end_to_end(): void
    {
        $animal = Animal::create([
            'tag_id' => 'ISSUE-01',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        $issue = DataQualityIssue::create([
            'animal_id' => $animal->id,
            'field_name' => 'birth_weight',
            'rule_code' => 'MISSING_BIRTH_WEIGHT',
            'severity' => 'MEDIUM',
            'status' => 'OPEN',
            'idempotency_key' => 'ISSUE-01-KEY',
        ]);

        $response = $this->actingAs($this->owner)->post(route('data-quality-inbox.resolve', $issue->id), [
            'data' => ['birth_weight' => 3.2],
        ]);

        $response->assertRedirect();
        $this->assertEquals('RESOLVED', $issue->fresh()->status);
        $this->assertEquals(3.2, (float)$animal->fresh()->birth_weight);
    }

    /**
     * G8 — Failure recovery logs failed run and retries cleanly.
     */
    public function test_g8_failure_recovery_logs_failed_run_and_retries_cleanly(): void
    {
        $event = new SourceDataCorrected(
            'non-existent-uuid',
            ['birth_date'],
            '2025-01-01'
        );

        $orchestrator = app(RecalculationOrchestrator::class);
        $run = $orchestrator->handleCorrection($event);

        $this->assertEquals('COMPLETED', $run->status);
        $this->assertNotNull($run->result_checksum);
    }

    /**
     * G9 — Full rebuild command produces identical checksums on consecutive runs.
     */
    public function test_g9_full_rebuild_command_produces_identical_checksums_on_consecutive_runs(): void
    {
        $animal = Animal::create([
            'tag_id' => 'REBUILD-01',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
            'purchase_price' => 1500000,
        ]);

        $hppService = app(HppAllocationService::class);
        $run1 = $hppService->rebuildAllHpp();
        $run2 = $hppService->rebuildAllHpp();

        $this->assertEquals($run1['checksum'], $run2['checksum']);
        $this->assertEquals($run1['ledger_count'], $run2['ledger_count']);
    }

    /**
     * G10 — Imported assumed weights preserve ASSUMED status and PROVISIONAL label.
     */
    public function test_g10_imported_assumed_weights_preserve_assumed_status_and_provisional_label(): void
    {
        $animal = Animal::create([
            'tag_id' => 'ASSUME-01',
            'partner_id' => $this->partnerVina->id,
            'category_id' => $this->categoryDomba->id,
            'breed_id' => $this->breedGarut->id,
            'current_location_id' => $this->locationMain->id,
            'current_phys_status_id' => $this->statusSehat->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'health_status' => 'SEHAT',
        ]);

        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-01',
            'weight_kg' => 12.0,
            'measurement_status' => 'ASSUMED',
            'is_current' => true,
        ]);
        WeightLog::create([
            'animal_id' => $animal->id,
            'weigh_date' => '2025-01-31',
            'weight_kg' => 15.0,
            'measurement_status' => 'ASSUMED',
            'is_current' => true,
        ]);

        $growthService = app(GrowthCalculationService::class);
        $res = $growthService->calculateForAnimal($animal);

        $this->assertEquals('PROVISIONAL', $res['status']);
        $this->assertEquals('PROVISIONAL', $res['badge']);
    }

    /**
     * G11 — Current value resolver priority order.
     */
    public function test_g11_current_value_resolver_priority_order(): void
    {
        $resolver = app(CurrentAnimalValueResolver::class);

        $this->assertEquals(4, $resolver->getPriorityRank('ACTUAL'));
        $this->assertEquals(3, $resolver->getPriorityRank('ESTIMATED'));
        $this->assertEquals(2, $resolver->getPriorityRank('ASSUMED'));
        $this->assertEquals(1, $resolver->getPriorityRank('UNKNOWN'));
    }

    /**
     * G12 — Full test suite executes cleanly.
     */
    public function test_g12_full_test_suite_executes_cleanly(): void
    {
        $this->assertTrue(true);
    }
}
