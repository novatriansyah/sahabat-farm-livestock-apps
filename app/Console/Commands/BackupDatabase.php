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
        $compress = (bool) $this->option('compress');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = "backups/{$timestamp}";
        $sqlFilename = $compress ? 'database.sql.gz' : 'database.sql';
        $sqlRelativePath = "{$backupDir}/{$sqlFilename}";
        $manifestRelativePath = "{$backupDir}/manifest.json";
        $checksumRelativePath = "{$sqlRelativePath}.sha256";

        $this->info("Backing up database to disk [{$disk}]...");

        $storageDisk = Storage::disk($disk);
        $storageDisk->makeDirectory($backupDir);

        $tempStream = fopen('php://temp', 'r+');

        $write = function (string $data) use ($tempStream) {
            fwrite($tempStream, $data);
        };

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
                        $colList = '`' . implode('`, `', array_keys($firstRow)) . '`';
                    }

                    $valRows = [];
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $vals = [];
                        foreach ($rowArray as $colName => $val) {
                            if ($val === null) {
                                $vals[] = 'NULL';
                            } elseif ($stringColumns[$colName] ?? false) {
                                $vals[] = $pdo->quote((string) $val);
                            } elseif (is_numeric($val)) {
                                $vals[] = (string) $val;
                            } else {
                                $vals[] = $pdo->quote((string) $val);
                            }
                        }
                        $valRows[] = '(' . implode(', ', $vals) . ')';
                        $count++;
                    }

                    $write("INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $valRows) . ";\n\n");
                });

                $recordCounts[$table] = $count;
                $this->line("  {$table}: {$count} rows");
            }

            $write("SET FOREIGN_KEY_CHECKS = 1;\n");

            rewind($tempStream);
            $rawSqlContent = stream_get_contents($tempStream);
            fclose($tempStream);

            if ($compress) {
                $finalBytes = gzencode($rawSqlContent, 9);
            } else {
                $finalBytes = $rawSqlContent;
            }

            $storageDisk->put($sqlRelativePath, $finalBytes);

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

        // Compute external SHA256 checksum over exact stored file bytes
        $storedContent = $storageDisk->get($sqlRelativePath);
        $sha256 = hash('sha256', $storedContent);

        // Store sidecar SHA-256 file
        $storageDisk->put($checksumRelativePath, "{$sha256}  {$sqlFilename}\n");

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
                'sha256' => "{$sqlFilename}.sha256",
            ],
        ];

        $storageDisk->put($manifestRelativePath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Backup completed successfully!");
        $this->info("  SQL:      {$sqlRelativePath}");
        $this->info("  Manifest: {$manifestRelativePath}");
        $this->info("  Checksum: {$checksumRelativePath} ({$sha256})");

        return Command::SUCCESS;
    }

    private function getCommitHash(): string
    {
        try {
            $headFile = base_path('.git/HEAD');
            if (!file_exists($headFile)) {
                return 'unknown';
            }
            $head = trim(file_get_contents($headFile));
            if (str_starts_with($head, 'ref: ')) {
                $ref = substr($head, 5);
                $refFile = base_path(".git/{$ref}");
                if (file_exists($refFile)) {
                    return trim(file_get_contents($refFile));
                }
            }
            return strlen($head) === 40 ? $head : 'unknown';
        } catch (\Throwable $e) {
            return 'unknown';
        }
    }
}