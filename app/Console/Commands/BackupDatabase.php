<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--disk=local : Disk to store backup}
                            {--compress : Gzip the SQL output}';

    protected $description = 'Backup database to SQL file with manifest (no exec, no mysqldump)';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $compress = $this->option('compress');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = "backups/{$timestamp}";
        $sqlFile = "{$backupDir}/database.sql";
        $manifestFile = "{$backupDir}/manifest.json";

        $this->info("Backing up database to disk [{$disk}]...");

        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";

        $output = "-- Backup created: " . now()->toIso8601String() . "\n";
        $output .= "-- Laravel: " . app()->version() . "\n";
        $output .= "-- Database: {$dbName}\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $recordCounts = [];
        $tableCount = 0;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;
            $tableCount++;

            // Get CREATE TABLE
            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            $createStmt = $createResult[0]->{'Create Table'};
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $output .= $createStmt . ";\n\n";

            // Get data
            $rows = DB::table($table)->get();
            $count = count($rows);
            $recordCounts[$table] = $count;

            if ($count > 0) {
                $columns = array_keys((array) $rows[0]);
                $colList = '`' . implode('`,`', $columns) . '`';

                $chunks = array_chunk($rows->all(), 500);
                foreach ($chunks as $chunk) {
                    $values = [];
                    foreach ($chunk as $row) {
                        $row = (array) $row;
                        $escaped = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $escaped[] = 'NULL';
                            } elseif (is_numeric($val)) {
                                $escaped[] = $val;
                            } else {
                                $escaped[] = "'" . str_replace("'", "''", $val) . "'";
                            }
                        }
                        $values[] = '(' . implode(',', $escaped) . ')';
                    }
                    $output .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";
                    $output .= implode(",\n", $values) . ";\n\n";
                }
            }

            $this->line("  {$table}: {$count} rows");
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        // Write SQL file
        if ($compress) {
            $sqlPath = "{$backupDir}/database.sql.gz";
            Storage::disk($disk)->put($sqlPath, gzencode($output, 9));
            $this->info("Compressed SQL written to {$sqlPath}");
        } else {
            Storage::disk($disk)->put($sqlFile, $output);
            $this->info("SQL written to {$sqlFile}");
        }

        // Generate SHA256
        $sha256 = hash('sha256', $output);

        // Write manifest
        $manifest = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'app_version' => app()->version(),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => $dbName,
            'table_count' => $tableCount,
            'record_counts' => $recordCounts,
            'total_records' => array_sum($recordCounts),
            'sha256' => $sha256,
            'compressed' => $compress,
            'files' => [
                'sql' => $compress ? "database.sql.gz" : "database.sql",
                'manifest' => 'manifest.json',
            ],
        ];

        Storage::disk($disk)->put($manifestFile, json_encode($manifest, JSON_PRETTY_PRINT));
        $this->info("Manifest written to {$manifestFile}");
        $this->info("SHA256: {$sha256}");
        $this->info("Backup complete. Total records: " . array_sum($recordCounts));

        return Command::SUCCESS;
    }
}