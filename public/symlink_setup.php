<?php
// Place this file in your 'public' folder (e.g. public_html/public/symlink_setup.php)
// Access it via: yoursite.com/symlink_setup.php

$target = __DIR__ . '/../storage/app/public';
$shortcut = __DIR__ . '/storage';

echo "Target: $target<br>";
echo "Shortcut: $shortcut<br><br>";

if (file_exists($shortcut)) {
    echo "❌ Link already exists (or a folder named 'storage' already exists). Please delete it first if it is incorrect.";
} elseif (!file_exists($target)) {
    echo "❌ Target directory does not exist. Check your structure.";
} else {
    try {
        // Try native symlink
        if (symlink($target, $shortcut)) {
            echo "✅ Success! Symlink created.";
        } else {
            echo "❌ Failed. The system returned false.";
        }
    } catch (Throwable $e) {
        echo "❌ Error: " . $e->getMessage();
        echo "<br>Tip: If checks fail, your hosting might block the 'symlink' function completely.";
    }
}
