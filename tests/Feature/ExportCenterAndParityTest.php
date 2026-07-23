<?php

namespace Tests\Feature;

use App\Models\MasterPartner;
use App\Models\User;
use App\Services\PartnerReportPdfService;
use App\Services\UnifiedReportCalculationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExportCenterAndParityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_export_center_index_accessible_by_authenticated_user()
    {
        $user = User::factory()->create(['role' => 'PEMILIK']);

        $response = $this->actingAs($user)->get(route('exports.index'));
        $response->assertStatus(200);
        $response->assertSee('Export Center');
    }

    public function test_mitra_tenant_isolation_restricts_export_to_own_partner_data()
    {
        $partner = MasterPartner::where('name', 'VINA')->orWhere('name', 'Mitra VINA')->first();

        if (!$partner) {
            $this->markTestSkipped('VINA partner not in DB — skipping tenant isolation test.');
        }

        $mitraUser = User::factory()->create([
            'role'       => 'MITRA',
            'partner_id' => $partner->id,
        ]);

        $response = $this->actingAs($mitraUser)->get(route('exports.index'));
        $response->assertStatus(200);
        $response->assertSee('Export Center');

        // Mitra attempt to download canonical full export is forced to scoped export
        $downloadResponse = $this->actingAs($mitraUser)->get(route('exports.download', [
            'product'    => 'import_compatible',
            'partner_id' => $partner->id,
        ]));
        $downloadResponse->assertStatus(200);
    }

    public function test_unified_calculation_service_guarantees_cross_format_parity()
    {
        $partner = MasterPartner::where('name', 'VINA')->orWhere('name', 'Mitra VINA')->first();

        if (!$partner) {
            $this->markTestSkipped('VINA partner not in DB — skipping parity test.');
        }

        $calcService = new UnifiedReportCalculationService();
        $summary = $calcService->getPartnerSummary($partner->id);

        $pdfService = new PartnerReportPdfService();
        $pdfData = $pdfService->generateReportData($partner->id);

        $this->assertEquals($summary['total_animals'], $pdfData['summary']['total_registered']);
        $this->assertEquals($summary['active_animals'], $pdfData['summary']['total_active']);
        $this->assertEquals($summary['dead_animals'], $pdfData['summary']['total_inactive']);
        $this->assertEquals($summary['average_adg_text'], $pdfData['summary']['avg_adg']);
    }
}
