<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\DataQualityIssue;
use App\Models\MasterPartner;
use App\Models\User;
use App\Services\MissingDataGovernanceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MissingDataGovernanceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_missing_data_rule_evaluation_generates_issues()
    {
        $service = new MissingDataGovernanceService();
        $issuesCreated = $service->scanAndGenerateIssues();

        $this->assertGreaterThan(0, DataQualityIssue::count());
        $this->assertDatabaseHas('data_quality_issues', [
            'status' => 'OPEN',
        ]);
    }

    public function test_conditional_process_blocking_prevents_blocked_operations()
    {
        $animal = Animal::where('tag_id', '010')->first();

        if (!$animal) {
            $this->markTestSkipped('Animal 010 not in DB — skipping blocking test.');
        }

        // Create a critical missing birth_weight issue blocking SALE and TRANSFER
        DataQualityIssue::create([
            'id'                  => (string) \Illuminate\Support\Str::uuid(),
            'issue_code'          => 'MISSING_BIRTH_WEIGHT',
            'animal_id'           => $animal->id,
            'field_name'          => 'birth_weight',
            'severity'            => 'CRITICAL_FOR_FINALIZATION',
            'status'              => 'OPEN',
            'blocked_processes'   => ['SALE', 'TRANSFER'],
            'assigned_role'       => 'PETERNAK',
            'idempotency_key'     => 'test-blocking-' . $animal->id,
        ]);

        $service = new MissingDataGovernanceService();
        $blockStatus = $service->checkProcessAllowed($animal->id, 'SALE');

        $this->assertTrue($blockStatus['is_blocked']);
        $this->assertContains('MISSING_BIRTH_WEIGHT', $blockStatus['blocking_issue_codes']);
    }

    public function test_user_can_complete_missing_data_via_governance_service()
    {
        $animal = Animal::where('tag_id', '010')->first();

        if (!$animal) {
            $this->markTestSkipped('Animal 010 not in DB — skipping completion test.');
        }

        $issue = DataQualityIssue::create([
            'id'                  => (string) \Illuminate\Support\Str::uuid(),
            'issue_code'          => 'MISSING_NOTES',
            'animal_id'           => $animal->id,
            'field_name'          => 'notes',
            'severity'            => 'OPTIONAL',
            'status'              => 'OPEN',
            'blocked_processes'   => [],
            'assigned_role'       => 'PETERNAK',
            'idempotency_key'     => 'test-completion-' . $animal->id,
        ]);

        $user = User::factory()->create(['role' => 'PETERNAK']);
        $service = new MissingDataGovernanceService();

        $resolved = $service->resolveIssue($issue->id, 'Ternak sehat catatan awal', $user->id);

        $this->assertTrue($resolved);
        $this->assertDatabaseHas('data_quality_issues', [
            'id'     => $issue->id,
            'status' => 'RESOLVED',
        ]);
        $this->assertEquals('Ternak sehat catatan awal', $animal->fresh()->notes);
    }
}
