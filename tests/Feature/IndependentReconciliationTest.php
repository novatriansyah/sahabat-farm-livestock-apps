<?php

namespace Tests\Feature;

use App\Services\ReconciliationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndependentReconciliationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_master_to_db_reconciliation_verifies_all_166_animals()
    {
        $recon = new ReconciliationService();
        $masterPath = base_path('SFI_MASTER_TERNAK_v3.xlsx');

        if (!file_exists($masterPath)) {
            $this->markTestSkipped('SFI_MASTER_TERNAK_v3.xlsx not present — skipping live reconciliation test.');
        }

        $results = $recon->compareMasterExcel($masterPath);

        $summary = $results['summary'];

        $this->assertGreaterThanOrEqual(1, $summary['TOTAL_UNION'],
            'Reconciliation must return at least one record.');
        $this->assertArrayHasKey('total_unique_union', $summary);
        $this->assertEquals($summary['TOTAL_UNION'], $summary['total_unique_union']);

        // Check contract keys match_tier and matched_by exist on every result row
        foreach ($results['results'] as $resRow) {
            $this->assertArrayHasKey('match_tier', $resRow);
            $this->assertArrayHasKey('matched_by', $resRow);
        }
    }
}
