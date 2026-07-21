<?php

namespace Tests\Unit;

use App\Exports\Sheets\ManifestSheet;
use App\Exports\Sheets\SummarySheet;
use App\Exports\Sheets\IndukanSheet;
use App\Exports\Sheets\AnakanSheet;
use App\Models\Animal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Tests\TestCase;

class ExportSheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_sheet_has_correct_schema_version()
    {
        $sheet = new ManifestSheet('1.0.0');
        $this->assertEquals('MANIFEST', $sheet->title());
        $data = $sheet->array();
        $keys = array_column($data, 0);
        $this->assertContains('schema_version', $keys);
    }

    public function test_manifest_sheet_implements_required_interfaces()
    {
        $sheet = new ManifestSheet('1.0.0');
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertInstanceOf(WithHeadings::class, $sheet);
    }

    public function test_summary_sheet_implements_required_interfaces()
    {
        $sheet = new SummarySheet([]);
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertInstanceOf(WithHeadings::class, $sheet);
    }

    public function test_summary_sheet_title()
    {
        $sheet = new SummarySheet([]);
        $this->assertEquals('REKAP', $sheet->title());
    }

    public function test_indukan_sheet_implements_required_interfaces()
    {
        $sheet = new IndukanSheet([]);
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertInstanceOf(WithHeadings::class, $sheet);
        $this->assertInstanceOf(WithColumnFormatting::class, $sheet);
    }

    public function test_indukan_sheet_title()
    {
        $sheet = new IndukanSheet([]);
        $this->assertEquals('INDUKAN', $sheet->title());
    }

    public function test_anakan_sheet_implements_required_interfaces()
    {
        $sheet = new AnakanSheet([]);
        $this->assertInstanceOf(WithTitle::class, $sheet);
        $this->assertInstanceOf(WithHeadings::class, $sheet);
        $this->assertInstanceOf(WithColumnFormatting::class, $sheet);
    }

    public function test_anakan_sheet_title()
    {
        $sheet = new AnakanSheet([]);
        $this->assertEquals('ANAKAN', $sheet->title());
    }

    public function test_indukan_sheet_has_tag_id_as_text_format()
    {
        $sheet = new IndukanSheet([]);
        $formats = $sheet->columnFormats();
        $this->assertArrayHasKey('B', $formats);
    }

    public function test_anakan_sheet_has_tag_id_as_text_format()
    {
        $sheet = new AnakanSheet([]);
        $formats = $sheet->columnFormats();
        $this->assertArrayHasKey('B', $formats);
    }
}