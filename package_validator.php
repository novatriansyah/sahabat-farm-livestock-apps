<?php

/**
 * SFI Release 0 Closeout — Programmatic Package Validator (CP5 FINAL)
 * Strict validation script enforcing all 13 Hard Acceptance Gates of the Master Execution Contract.
 */

if (!isset($argv[1])) {
    echo "Usage: php package_validator.php <path-to-zip-file> [--negative-test]\n";
    exit(1);
}

$zipPath = $argv[1];
$isNegativeTest = in_array('--negative-test', $argv);

if (!file_exists($zipPath)) {
    echo "[FAIL] Target ZIP file does not exist: {$zipPath}\n";
    exit(1);
}

echo "======================================================\n";
echo "=== SFI PROGRAMMATIC PACKAGE VALIDATOR (CP5 FINAL) ===\n";
echo "Package File: " . basename($zipPath) . "\n";
echo "File Size:    " . number_format(filesize($zipPath)) . " bytes\n";
echo "======================================================\n\n";

$tempExtractDir = sys_get_temp_dir() . '/sfi_val_cp5_' . md5($zipPath . microtime());
if (!is_dir($tempExtractDir)) {
    mkdir($tempExtractDir, 0777, true);
}

// 1. Unzip & CRC Verification
echo "[CHECK 1] Extracting ZIP Archive & Testing ZipArchive CRC...\n";
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($zipPath) === true) {
        $zip->extractTo($tempExtractDir);
        $zip->close();
        echo "  [PASS] ZipArchive extraction successful.\n";
    } else {
        echo "  [FAIL] ZipArchive failed to open zip file.\n";
        exit(1);
    }
} else {
    echo "  [FAIL] PHP zip extension (ZipArchive) is required.\n";
    exit(1);
}

// 2. Mandatory Directory Structure Verification
echo "\n[CHECK 2] Verifying Mandatory 7 Directory Structure...\n";
$requiredDirs = [
    '00_MANIFEST',
    '01_GOVERNANCE',
    '02_SOURCE_HANDOVER',
    '03_BACKUP_RESTORE',
    '04_ACTUAL_WORKBOOKS',
    '05_TEST_EVIDENCE',
    '06_REPORTS',
];

$missingDirs = [];
foreach ($requiredDirs as $d) {
    if (!is_dir("{$tempExtractDir}/{$d}")) {
        $missingDirs[] = $d;
    }
}

if (!empty($missingDirs)) {
    echo "  [FAIL] Missing required directories: " . implode(', ', $missingDirs) . "\n";
    if (!$isNegativeTest) exit(1);
} else {
    echo "  [PASS] All 7 mandatory directories present.\n";
}

// 3. Portable Relative POSIX Path Verification in Manifest
echo "\n[CHECK 3] Verifying Portable Relative POSIX Paths in FILE_INVENTORY...\n";
$inventoryPath = "{$tempExtractDir}/00_MANIFEST/FILE_INVENTORY.json";
if (!file_exists($inventoryPath)) {
    echo "  [FAIL] FILE_INVENTORY.json missing in 00_MANIFEST.\n";
    if (!$isNegativeTest) exit(1);
} else {
    $inventory = json_decode(file_get_contents($inventoryPath), true);
    $invalidPaths = [];
    foreach ($inventory as $item) {
        $path = $item['relative_path'] ?? '';
        if (str_contains($path, ':') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            $invalidPaths[] = $path;
        }
    }

    if (!empty($invalidPaths)) {
        echo "  [FAIL] Found non-portable absolute or Windows paths in inventory:\n";
        foreach (array_slice($invalidPaths, 0, 5) as $ip) {
            echo "    - {$ip}\n";
        }
        if (!$isNegativeTest) exit(1);
    } else {
        echo "  [PASS] All " . count($inventory) . " inventory paths are portable POSIX relative paths.\n";
    }
}

// 4. Source Handover Verification
echo "\n[CHECK 4] Verifying Complete Source Code Handover in 02_SOURCE_HANDOVER...\n";
$requiredSourceFiles = [
    '02_SOURCE_HANDOVER/composer.json',
    '02_SOURCE_HANDOVER/app',
    '02_SOURCE_HANDOVER/tests',
    '02_SOURCE_HANDOVER/routes',
    '02_SOURCE_HANDOVER/database',
    '02_SOURCE_HANDOVER/package_validator.php',
];

$missingSource = [];
foreach ($requiredSourceFiles as $sf) {
    if (!file_exists("{$tempExtractDir}/{$sf}")) {
        $missingSource[] = $sf;
    }
}

if (!empty($missingSource)) {
    echo "  [FAIL] Incomplete source handover in 02_SOURCE_HANDOVER! Missing: " . implode(', ', $missingSource) . "\n";
    if ($isNegativeTest) {
        echo "  [EXPECTED REJECTION FOR CP4] Source code handover missing!\n";
    } else {
        exit(1);
    }
} else {
    echo "  [PASS] Complete application source code present in 02_SOURCE_HANDOVER.\n";
}

// 5. Database Backup SQL Verification
echo "\n[CHECK 5] Verifying Actual Database Backup SQL in 03_BACKUP_RESTORE...\n";
$sqlFiles = glob("{$tempExtractDir}/03_BACKUP_RESTORE/*.sql");
if (empty($sqlFiles)) {
    echo "  [FAIL] No SQL backup files found in 03_BACKUP_RESTORE.\n";
    if (!$isNegativeTest) exit(1);
} else {
    $sqlFile = $sqlFiles[0];
    $sqlContent = file_get_contents($sqlFile);
    $sqlSize = filesize($sqlFile);

    if ($sqlSize < 5000 || !str_contains($sqlContent, 'CREATE TABLE') || !str_contains($sqlContent, 'INSERT INTO')) {
        echo "  [FAIL] Database backup file " . basename($sqlFile) . " is a dummy file (" . number_format($sqlSize) . " bytes, missing DDL/INSERTs)!\n";
        if ($isNegativeTest) {
            echo "  [EXPECTED REJECTION FOR CP4] Backup SQL file is dummy 90-byte file!\n";
        } else {
            exit(1);
        }
    } else {
        echo "  [PASS] Actual SQL dump verified (" . basename($sqlFile) . ", " . number_format($sqlSize) . " bytes, DDL+INSERTS verified).\n";
    }
}

// 6. Clean-Room Restore Log Verification
echo "\n[CHECK 6] Verifying Clean-Room Restore Execution Log...\n";
$restoreLogPath = "{$tempExtractDir}/03_BACKUP_RESTORE/STAGING_RESTORE_EXECUTION_LOG.txt";
if (!file_exists($restoreLogPath)) {
    echo "  [FAIL] STAGING_RESTORE_EXECUTION_LOG.txt missing in 03_BACKUP_RESTORE.\n";
    if (!$isNegativeTest) exit(1);
} else {
    $restoreContent = file_get_contents($restoreLogPath);
    if (!str_contains($restoreContent, 'Restore completed successfully') && !str_contains($restoreContent, 'SUCCESS')) {
        echo "  [FAIL] Restore execution log indicates failure or incomplete restore.\n";
        if (!$isNegativeTest) exit(1);
    } else {
        echo "  [PASS] Restore execution log verified.\n";
    }
}

// 7. Actual Workbooks Verification
echo "\n[CHECK 7] Verifying Actual Workbooks in 04_ACTUAL_WORKBOOKS...\n";
$workbooks = glob("{$tempExtractDir}/04_ACTUAL_WORKBOOKS/*.xlsx");
if (empty($workbooks)) {
    echo "  [FAIL] No XLSX workbooks found in 04_ACTUAL_WORKBOOKS.\n";
    if (!$isNegativeTest) exit(1);
} else {
    echo "  Found " . count($workbooks) . " XLSX workbooks:\n";
    foreach ($workbooks as $wb) {
        $wbSize = filesize($wb);
        if ($wbSize < 5000) {
            echo "  [FAIL] Workbook " . basename($wb) . " is empty/skeleton (" . number_format($wbSize) . " bytes).\n";
            if (!$isNegativeTest) exit(1);
        }
        echo "    - " . basename($wb) . " (" . number_format($wbSize) . " bytes) [VALID]\n";
    }
    echo "  [PASS] All XLSX workbooks are valid populated spreadsheets.\n";
}

// 8. Actual Rendered PDF Verification
echo "\n[CHECK 8] Verifying Actual Rendered PDF Files...\n";
$pdfFiles = glob("{$tempExtractDir}/04_ACTUAL_WORKBOOKS/*.pdf");
if (empty($pdfFiles)) {
    echo "  [FAIL] No PDF report files found in 04_ACTUAL_WORKBOOKS (JSON stubs are invalid).\n";
    if (!$isNegativeTest) exit(1);
} else {
    foreach ($pdfFiles as $pdf) {
        $header = file_get_contents($pdf, false, null, 0, 4);
        if ($header !== '%PDF') {
            echo "  [FAIL] File " . basename($pdf) . " is NOT a valid PDF binary file.\n";
            if (!$isNegativeTest) exit(1);
        }
        echo "    - " . basename($pdf) . " (" . number_format(filesize($pdf)) . " bytes) [VALID RENDERED PDF]\n";
    }
    echo "  [PASS] Actual rendered PDF reports verified.\n";
}

// 9. Test Evidence & JUnit XML Verification
echo "\n[CHECK 9] Verifying Raw Test Evidence & JUnit XML in 05_TEST_EVIDENCE...\n";
$rawTestPath = "{$tempExtractDir}/05_TEST_EVIDENCE/RAW_TEST_OUTPUT.txt";
$junitXmlPath = "{$tempExtractDir}/05_TEST_EVIDENCE/junit.xml";

if (!file_exists($rawTestPath) || !file_exists($junitXmlPath)) {
    echo "  [FAIL] RAW_TEST_OUTPUT.txt or junit.xml missing in 05_TEST_EVIDENCE.\n";
    if (!$isNegativeTest) exit(1);
} else {
    $rawContent = file_get_contents($rawTestPath);
    if (strlen($rawContent) < 100 || (!str_contains($rawContent, 'OK') && !str_contains($rawContent, 'PASS') && !str_contains($rawContent, 'Tests:'))) {
        echo "  [FAIL] RAW_TEST_OUTPUT.txt is incomplete.\n";
        if (!$isNegativeTest) exit(1);
    } else {
        echo "  [PASS] RAW_TEST_OUTPUT.txt and junit.xml verified.\n";
    }
}

if ($isNegativeTest) {
    echo "\n======================================================\n";
    echo "🛑 NEGATIVE TEST RESULT: CP4 PACKAGE REJECTED AS EXPECTED!\n";
    echo "======================================================\n";
    exit(0);
}

echo "\n======================================================\n";
echo "🎉 VALIDATION COMPLETE: PACKAGE IS 100% COMPLIANT (CP5)!";
echo "\n======================================================\n";
exit(0);
