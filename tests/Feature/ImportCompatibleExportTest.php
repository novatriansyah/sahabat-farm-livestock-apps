<?php

namespace Tests\Feature;

use App\Exports\BlankImportTemplate;
use App\Exports\ImportCompatibleAnimalExport;
use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\User;
use App\Schemas\AnimalTemplateSchema;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ImportCompatibleExportTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create(['role' => 'PEMILIK']);
        $partner = MasterPartner::create(['name' => 'Mitra Berkah', 'contact_info' => 'berkah@partner.com']);
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::create(['name' => 'Garut', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang B', 'type' => 'Koloni']);
        $statusSehat = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        Animal::create([
            'id' => '00000000-0000-0000-0000-000000000010',
            'tag_id' => '036',
            'owner_id' => $owner->id,
            'partner_id' => $partner->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $statusSehat->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'is_active' => true,
            'google_drive_link' => 'https://drive.google.com/folder/sample036',
        ]);
    }

    public function test_schema_exact_equality_between_template_and_export(): void
    {
        $template = new BlankImportTemplate();
        $templateSheets = $template->sheets();
        $this->assertArrayHasKey('DATA_TERNAK', $templateSheets);

        $templateHeaders = $templateSheets['DATA_TERNAK']->headings();

        $export = new ImportCompatibleAnimalExport();
        $exportSheets = $export->sheets();
        $this->assertArrayHasKey('DATA_TERNAK', $exportSheets);

        $exportHeaders = $exportSheets['DATA_TERNAK']->headings();

        $this->assertEquals($templateHeaders, $exportHeaders);
        $this->assertEquals(AnimalTemplateSchema::getHeaders(), $exportHeaders);
    }

    public function test_leading_zero_tags_are_true_strings(): void
    {
        $export = new ImportCompatibleAnimalExport();
        $rows = $export->sheets()['DATA_TERNAK']->collection();

        $row = $rows->firstWhere('tag_id', '036');
        $this->assertNotNull($row);
        $this->assertSame('036', $row['tag_id']);
        $this->assertSame('https://drive.google.com/folder/sample036', $row['gdrive_folder_url']);
    }
}
