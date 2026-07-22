<?php

namespace Tests\Unit;

use App\Exports\BlankImportTemplate;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BlankTemplateTest extends TestCase
{
    public function test_blank_import_template_executes_zero_database_queries(): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        $template = new BlankImportTemplate();
        $sheets = $template->sheets();

        foreach ($sheets as $sheet) {
            if (method_exists($sheet, 'array')) {
                $sheet->array();
            }
        }

        $queryLog = DB::getQueryLog();
        $this->assertEmpty($queryLog, "BlankImportTemplate must execute zero database queries upon instantiation and data generation.");
    }
}
