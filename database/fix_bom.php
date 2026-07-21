<?php
$files = glob(__DIR__ . '/../app/Exports/Sheets/*.php');
$files[] = __DIR__ . '/../app/Exports/AnimalMasterExport.php';
$count = 0;
foreach ($files as $f) {
    $c = file_get_contents($f);
    // Check for UTF-8 BOM (EF BB BF)
    if (strlen($c) > 3 && ord($c[0]) === 0xEF && ord($c[1]) === 0xBB && ord($c[2]) === 0xBF) {
        file_put_contents($f, substr($c, 3));
        echo "Fixed: " . basename($f) . "\n";
        $count++;
    }
}
echo "Total files fixed: $count\n";