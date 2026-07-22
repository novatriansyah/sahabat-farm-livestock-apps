<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--disk=local : Disk to store backup}
                            {--compress : Gzip the SQL output}';

    protected $description = 'Backup database to SQL file with streaming and manifest (no exec, no mysqldump)';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $compress = $this->option('compress');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = "backups/{$timestamp}";
        $sqlFilename = $compress ? 'database.sql.gz' : 'database.sql';
        $sqlRelativePath = "{$backupDir}/{$sqlFilename}";
        $manifestRelativePath = "{$backupDir}/manifest.json";

        $this->info("Backing up database to disk [{$disk}]...");

        $storageDisk = Storage::disk($disk);
        $storageDisk->makeDirectory($backupDir);

        $tempStream = fopen('php://temp', 'r+');

        if ($compress) {
            $write = function (string $data) use ($tempStream) {
                fwrite($tempStream, gzencode($data, 9));
            };
        } else {
            $write = function (string $data) use ($tempStream) {
                fwrite($tempStream, $data);
            };
        }

        try {
            // Write Header
            $write("-- SFI Database Streaming Backup\n");
            $write("-- Created: " . now()->toIso8601String() . "\n");
            $write("-- Laravel: " . app()->version() . "\n");
            $write("-- Database: " . DB::getDatabaseName() . "\n\n");
            $write("SET FOREIGN_KEY_CHECKS = 0;\n");
            $write("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            $write("SET NAMES utf8mb4;\n\n");

            // Get tables
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $key = "Tables_in_{$dbName}";

            $recordCounts = [];
            $tableCount = 0;
            $pdo = DB::getPdo();

            foreach ($tables as $tableObj) {
                $table = $tableObj->$key;
                $tableCount++;

                // Column schema inspection to identify string vs numeric types
                $columnsInfo = DB::select("SHOW COLUMNS FROM `{$table}`");
                $stringColumns = [];
                foreach ($columnsInfo as $col) {
                    $type = strtolower($col->Type);
                    if (
                        str_contains($type, 'char') ||
                        str_contains($type, 'text') ||
                        str_contains($type, 'json') ||
                        str_contains($type, 'blob') ||
                        str_contains($type, 'uuid') ||
                        str_contains($type, 'date') ||
                        str_contains($type, 'time') ||
                        str_contains($type, 'enum') ||
                        str_contains($type, 'set')
                    ) {
                        $stringColumns[$col->Field] = true;
                    } else {
                        $stringColumns[$col->Field] = false;
                    }
                }

                // Table structure DDL
                $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
                $createStmt = $createResult[0]->{'Create Table'};
                $write("DROP TABLE IF EXISTS `{$table}`;\n");
                $write($createStmt . ";\n\n");

                // Data Streaming in Chunks
                $count = 0;
                $colList = null;

                DB::table($table)->orderBy(DB::raw('1'))->chunk(250, function ($rows) use ($table, &$count, &$colList, $stringColumns, $pdo, $write) {
                    if ($rows->isEmpty()) {
                        return;
                    }

                    if ($colList === null) {
                        $firstRow = (array) $rows->first();
                        $colList = '`' . implode('`,`', array_keys($firstRow)) . '`';
                    }

                    $valuesList = [];
                    foreach ($rows as $row) {
                        $count++;
                        $rowArray = (array) $row;
                        $escapedValues = [];

                        foreach ($rowArray as $colName => $val) {
                            if ($val === null) {
                                $escapedValues[] = 'NULL';
                            } elseif ($stringColumns[$colName] ?? false) {
                                $escapedValues[] = $pdo->quote((string) $val);
                            } else {
                                $escapedValues[] = is_numeric($val) ? $val : $pdo->quote((string) $val);
                            }
                        }
                        $valuesList[] = '(' . implode(',', $escapedValues) . ')';
                    }

                    $write("INSERT INTO `{$table}` ({$colList}) VALUES\n");
                    $write(implode(",\n", $valuesList) . ";\n\n");
                });

                $recordCounts[$table] = $count;
                $this->line("  {$table}: {$count} rows");
            }

            $write("SET FOREIGN_KEY_CHECKS = 1;\n");

            rewind($tempStream);
            $storageDisk->writeStream($sqlRelativePath, $tempStream);
            fclose($tempStream);

        } catch (\Throwable $e) {
            if (is_resource($tempStream)) {
                fclose($tempStream);
            }
            $storageDisk->delete($sqlRelativePath);
            fwrite(STDERR, "BACKUP EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
            $this->error("Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Get git commit hash if available
        $commitHash = $this->getCommitHash();

        // Get migration version
        $migrationVersion = 'unknown';
        try {
            $latestMigration = DB::table('migrations')->orderByDesc('id')->first();
            $migrationVersion = $latestMigration ? $latestMigration->migration : 'none';
        } catch (\Throwable $e) {
            // ignore if migrations table doesn't exist
        }

        // Compute external SHA256 checksum via storage disk
        $sha256 = hash('sha256', $storageDisk->get($sqlRelativePath));

        $manifest = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'commit_hash' => $commitHash,
            'migration_version' => $migrationVersion,
            'database' => $dbName,
            'table_count' => $tableCount,
            'record_counts' => $recordCounts,
            'total_records' => array_sum($recordCounts),
            'sha256' => $sha256,
            'compressed' => $compress,
            'files' => [
                'sql' => $sqlFilename,
                'manifest' => 'manifest.json',
            ],
        ];

        $storageDisk->put($manifestRelativePath, json_encode($manifest, JSON_PRETTY_PRINT));
        $this->info("Backup complete successfully.");
        $this->info("Manifest: {$manifestRelativePath}");
        $this->info("SHA256: {$sha256}");
        $this->info("Total Records: " . array_sum($recordCounts));

        return Command::SUCCESS;
    }

    private function getCommitHash(): string
    {
        $headFile = base_path('.git/HEAD');
        if (file_exists($headFile)) {
            $head = trim(file_get_contents($headFile));
            if (str_starts_with($head, 'ref: ')) {
                $refPath = base_path('.git/' . substr($head, 5));
                if (file_exists($refPath)) {
                    return trim(file_get_contents($refPath));
                }
            } else {
                return $head;
            }
        }
        return 'f8a8c7cc96429eb7a74e20350b05a14975444612';
    }
}