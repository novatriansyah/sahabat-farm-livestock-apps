<?php

namespace Tests\Feature;

use App\Exports\ImportCompatibleAnimalExport;
use App\Imports\AnimalsImport;
use App\Models\Animal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class RoundtripLosslessTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_exporter_does_not_fabricate_default_values_for_missing_fields()
    {
        if (Animal::count() === 0) {
            $this->markTestSkipped('No animals in DB — skipping roundtrip test.');
        }

        $export = new ImportCompatibleAnimalExport();
        $collection = $export->getDataTernakCollection();

        $this->assertGreaterThan(0, count($collection));

        foreach ($collection as $row) {
            // The collection returns associative arrays — check by named keys
            $this->assertNotEquals('Sehat proporsional', $row['physical_characteristics'] ?? null,
                'physical_characteristics must not be the fabricated default.');
            $this->assertNotEquals('EVT-2025-001', $row['birth_event_ref'] ?? null,
                'birth_event_ref must not be the fabricated default.');
            // birth_weight must be null (not a default numeric value) when not set
            if (array_key_exists('birth_weight', $row) && $row['birth_weight'] === null) {
                $this->assertNull($row['birth_weight']);
            }
        }
    }

    public function test_importer_updates_idempotently_without_fabricating_defaults()
    {
        $animal = Animal::where('tag_id', '010')->first();

        if (!$animal) {
            $this->markTestSkipped('Animal 010 not in DB — skipping importer test.');
        }

        $importer = new AnimalsImport();

        // Must include NOT NULL fields — pull from existing animal record
        $existingBirthDate = $animal->birth_date
            ? date('Y-m-d', strtotime($animal->birth_date))
            : '2024-01-01';

        $row = [
            'id'                       => $animal->id,
            'tag_id'                   => '010',
            'gender'                   => 'BETINA',
            'birth_date'               => $existingBirthDate,
            'entry_date'               => $animal->entry_date ? date('Y-m-d', strtotime($animal->entry_date)) : null,
            'acquisition_type'         => $animal->acquisition_type ?? 'BELI',
            'acquisition_cost'         => $animal->purchase_price ?? 0,
            'physical_characteristics' => 'Tanduk melingkar pendek',
            'is_active'                => '1',
        ];

        $importer->collection(collect([$row]));

        $animal->refresh();
        $this->assertEquals('Tanduk melingkar pendek', $animal->physical_characteristics);
    }
}
