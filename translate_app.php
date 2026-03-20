<?php

$dir = __DIR__ . '/app';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$files = [];
foreach ($iterator as $file) {
    if ($file->isFile() && strpos($file->getFilename(), '.php') !== false) {
        $files[] = $file->getPathname();
    }
}

$replacements = [
    "'PENDING'" => "'MENUNGGU'",
    "'SUCCESS'" => "'BERHASIL'",
    "'FAILED'" => "'GAGAL'",
    "'COMPLETED'" => "'SELESAI'",
    "'DEATH'" => "'MATI'",
    "'SALE'" => "'JUAL'",
    "'SOLD'" => "'TERJUAL'",
    "'DECEASED'" => "'MATI'",
    "=== 'SALE'" => "=== 'JUAL'",
];

$count = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;

    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Translated: " . basename($file) . "\n";
        $count++;
    }
}

echo "Total app files translated: $count\n";

