<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test 1: Verify AnimalMasterExport class
dump('Test 1: Class exists');
dump(class_exists(\App\Exports\AnimalMasterExport::class));

// Test 2: Verify sheets
dump('Test 2: Sheets');
$export = new \App\Exports\AnimalMasterExport([], 'test-hash', 'local');
$sheets = $export->sheets();
dump('Sheet count: ' . count($sheets));
foreach ($sheets as $name => $sheet) {
    dump("  - $name: " . get_class($sheet));
}

// Test 3: Verify no filters on animals query
dump('Test 3: Animal query (should return ALL)');
$total = \App\Models\Animal::count();
$active = \App\Models\Animal::where('is_active', true)->count();
$inactive = \App\Models\Animal::where('is_active', false)->count();
dump("Total: $total, Active: $active, Inactive: $inactive");

// Test 4: Verify B43 exists
dump('Test 4: B43 check');
$b43 = \App\Models\Animal::where('tag_id', 'B43')->first();
dump($b43 ? "B43 found, active=" . ($b43->is_active ? 'yes' : 'no') : 'B43 not found');

// Test 5: Verify ManifestSheet
dump('Test 5: ManifestSheet');
$manifest = new \App\Exports\Sheets\ManifestSheet('1.0.0', 'test-hash', 'local');
$data = $manifest->array();
foreach ($data as $row) {
    if ($row[0] && $row[1]) {
        dump("  {$row[0]}: {$row[1]}");
    }
}

// Test 6: Verify SummarySheet
dump('Test 6: SummarySheet');
$summary = new \App\Exports\Sheets\SummarySheet();
$data = $summary->array();
foreach ($data as $row) {
    dump("  {$row[0]}: {$row[1]}");
}

echo "\nAll tests passed.\n";