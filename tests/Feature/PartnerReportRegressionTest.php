<?php

namespace Tests\Feature;

use App\Exports\PartnerReportExport;
use App\Models\Animal;
use App\Models\MasterPartner;
use App\Models\WeightLog;
use App\Services\PartnerMetricsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class PartnerReportRegressionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Gate 1 & X-01: Every partner report XLSX file must contain at least 4 embedded charts.
     */
    public function test_partner_report_xlsx_contains_at_least_four_embedded_charts()
    {
        if (!class_exists('\ZipArchive') || !extension_loaded('zip')) {
            $this->markTestSkipped('PHP zip extension not enabled in CLI environment.');
        }

        $partner = MasterPartner::firstOrCreate(['name' => 'FAHRI'], ['contact_info' => 'Mitra FAHRI']);

        $tempPath = storage_path('app/testing_PARTNER_REPORT_FAHRI.xlsx');
        $raw = Excel::raw(new PartnerReportExport((string) $partner->id), \Maatwebsite\Excel\Excel::XLSX);
        file_put_contents($tempPath, $raw);

        $reader = IOFactory::createReaderForFile($tempPath);
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($tempPath);
        $totalCharts = 0;
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $totalCharts += count($sheet->getChartCollection());
        }

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $this->assertGreaterThanOrEqual(4, $totalCharts, "Partner report XLSX must contain at least 4 embedded charts, found {$totalCharts}.");
    }

    /**
     * Test Gate 2 & X-02: PartnerReportExport.php must NOT contain hardcoded 125 ADG.
     */
    public function test_partner_report_export_does_not_contain_hardcoded_125_adg()
    {
        $filePath = base_path('app/Exports/PartnerReportExport.php');
        $content = file_get_contents($filePath);

        $this->assertStringNotContainsString("'calculated_adg_g_day'  => 125", $content, "Hardcoded 125 ADG fallback found in PartnerReportExport.php");
        $this->assertStringNotContainsString("'calculated_adg_g_day' => 125", $content, "Hardcoded 125 ADG fallback found in PartnerReportExport.php");
        $this->assertStringNotContainsString("'125 g/hari'", $content, "Hardcoded 125 g/hari string found in PartnerReportExport.php");
    }

    /**
     * Test Gate 2 & X-03: PartnerReportExport.php must NOT contain hardcoded 45000 treatment cost.
     */
    public function test_partner_report_export_does_not_contain_hardcoded_45000_treatment_cost()
    {
        $filePath = base_path('app/Exports/PartnerReportExport.php');
        $content = file_get_contents($filePath);

        $this->assertStringNotContainsString('45000', $content, "Hardcoded 45000 treatment cost found in PartnerReportExport.php");
    }

    /**
     * Test Gate 3 & X-04: KELAHIRAN_REPRODUKSI sheet must be populated with partner animal birth records (rows > 1).
     */
    public function test_kelahiran_reproduksi_sheet_is_populated_with_partner_birth_records()
    {
        $partner = MasterPartner::firstOrCreate(['name' => 'FAHRI'], ['contact_info' => 'Mitra FAHRI']);

        $cat = \App\Models\MasterCategory::firstOrCreate(['name' => 'Domba']);
        $breed = \App\Models\MasterBreed::firstOrCreate(['name' => 'Garut'], ['category_id' => $cat->id]);
        $loc = \App\Models\MasterLocation::firstOrCreate(['name' => 'Kandang Utama'], ['type' => 'Kandang']);
        $phys = \App\Models\MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);
        $catId = $cat->id;
        $breedId = $breed->id;
        $locId = $loc->id;
        $physId = $phys->id;

        // Create at least one animal for this partner if none exist
        if (Animal::where('partner_id', $partner->id)->count() === 0) {
            Animal::create([
                'tag_id'                 => 'TEST-TAG-REPRO-1',
                'gender'                 => 'BETINA',
                'partner_id'             => $partner->id,
                'category_id'            => $catId,
                'breed_id'               => $breedId,
                'current_location_id'    => $locId,
                'current_phys_status_id' => $physId,
                'birth_date'             => '2025-01-15',
                'birth_weight'           => 3.5,
                'purchase_price'         => 1000000,
                'entry_date'             => '2025-01-15',
                'is_active'              => true,
            ]);
        }

        if (!class_exists('\ZipArchive') || !extension_loaded('zip')) {
            $this->markTestSkipped('PHP zip extension not enabled in CLI environment.');
        }

        $tempPath = storage_path('app/testing_repro_FAHRI.xlsx');
        $raw = Excel::raw(new PartnerReportExport((string) $partner->id), \Maatwebsite\Excel\Excel::XLSX);
        file_put_contents($tempPath, $raw);

        $spreadsheet = IOFactory::load($tempPath);
        $reproSheet = $spreadsheet->getSheetByName('KELAHIRAN_REPRODUKSI');

        $this->assertNotNull($reproSheet, "Sheet KELAHIRAN_REPRODUKSI missing in PartnerReportExport.");
        $highestRow = $reproSheet->getHighestDataRow();

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $this->assertGreaterThan(1, $highestRow, "KELAHIRAN_REPRODUKSI sheet must contain data rows (> 1), found {$highestRow}.");
    }

    /**
     * Test Gate 6 & Data-Truth: Insufficient weight data (< 2 weight logs) must return TIDAK DAPAT DIHITUNG (null value).
     */
    public function test_insufficient_weight_data_returns_tidak_dapat_dihitung()
    {
        $cat = \App\Models\MasterCategory::firstOrCreate(['name' => 'Domba']);
        $breed = \App\Models\MasterBreed::firstOrCreate(['name' => 'Garut'], ['category_id' => $cat->id]);
        $loc = \App\Models\MasterLocation::firstOrCreate(['name' => 'Kandang Utama'], ['type' => 'Kandang']);
        $phys = \App\Models\MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);
        $catId = $cat->id;
        $breedId = $breed->id;
        $locId = $loc->id;
        $physId = $phys->id;
        $partner = MasterPartner::firstOrCreate(['name' => 'TEST_PARTNER_ADG'], ['contact_info' => 'Test Partner']);

        $animal = Animal::create([
            'tag_id'                 => 'TEST-TAG-ADG-1',
            'gender'                 => 'JANTAN',
            'partner_id'             => $partner->id,
            'category_id'            => $catId,
            'breed_id'               => $breedId,
            'current_location_id'    => $locId,
            'current_phys_status_id' => $physId,
            'birth_date'             => '2025-01-15',
            'purchase_price'         => 1000000,
            'entry_date'             => '2025-01-15',
            'is_active'              => true,
        ]);

        // Add only 1 weight log
        WeightLog::create([
            'animal_id'  => $animal->id,
            'weigh_date' => '2025-01-01',
            'weight_kg'  => 25.0,
        ]);

        $service = new PartnerMetricsService();
        $adgValue = $service->averageAdgGramPerDay($partner->id);
        $displayText = $service->display($adgValue, 'g/hari');

        $this->assertNull($adgValue, "ADG value for single weight log must be null.");
        $this->assertEquals('TIDAK DAPAT DIHITUNG', $displayText, "ADG display text for single weight log must be TIDAK DAPAT DIHITUNG.");
    }
}
