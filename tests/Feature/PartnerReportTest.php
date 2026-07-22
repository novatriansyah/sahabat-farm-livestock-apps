<?php

namespace Tests\Feature;

use App\Exports\PartnerReportExport;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use App\Services\PartnerReportPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerReportTest extends TestCase
{
    use RefreshDatabase;

    private MasterPartner $partner;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create(['role' => 'PEMILIK']);
        $this->partner = MasterPartner::create(['name' => 'Mitra Sukses', 'contact_info' => 'sukses@partner.com']);

        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::create(['name' => 'Garut', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang A', 'type' => 'Koloni']);
        $statusSehat = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        Animal::create([
            'id' => '00000000-0000-0000-0000-000000000011',
            'tag_id' => 'SUKSES-01',
            'owner_id' => $owner->id,
            'partner_id' => $this->partner->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
        ]);
    }

    public function test_xlsx_and_pdf_kpi_reconciliation(): void
    {
        $export = new PartnerReportExport((string) $this->partner->id);
        $sheets = $export->sheets();

        $this->assertCount(9, $sheets);
        $this->assertArrayHasKey('RINGKASAN_MITRA', $sheets);
        $this->assertArrayHasKey('DAFTAR_TERNAK_AKTIF', $sheets);

        $activeSheet = $sheets['DAFTAR_TERNAK_AKTIF'];
        $xlsxCount = $activeSheet->collection()->count();

        $pdfService = new PartnerReportPdfService();
        $pdfData = $pdfService->generateReportData((string) $this->partner->id);

        $this->assertEquals($xlsxCount, $pdfData['summary']['total_active']);
        $this->assertEquals(1, $pdfData['summary']['total_active']);
        $this->assertEquals('PRELIMINARY / UNVERIFIED', $pdfData['summary']['hpp_status_label']);
    }
}
