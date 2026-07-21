<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=sahabat_farm', 'root', '');
    $r = $pdo->query('SHOW TABLES');
    echo "Tables:\n";
    while ($row = $r->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }
    echo "\nMigrations ran: " . $pdo->query("SELECT COUNT(*) FROM migrations")->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}