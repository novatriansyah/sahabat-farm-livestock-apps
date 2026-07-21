<?php
$files = glob(__DIR__ . '/../app/Exports/Sheets/*.php');
$files[] = __DIR__ . '/../app/Exports/AnimalMasterExport.php';
$found = 0;
foreach ($files as $f) {
    $c = file_get_contents($f);
    if (strlen($c) > 3 && ord($c[0]) === 0xEF && ord($c[1]) === 0xBB && ord($c[2]) === 0xBF) {
        echo "BOM: " . basename($f) . "\n";
        $found++;
    }
}
echo "Files with BOM: $found\n";
if ($found === 0) {
    echo "All clean. Running test...\n";
    require __DIR__ . '/test_export.php';
}