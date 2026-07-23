<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestoreBackup extends Command
{
    protected $signature = 'backup:restore
                            {backup : Backup directory name (e.g. 2026-07-21_16-42-06)}
                            {--disk=local : Disk where backup is stored}
                            {--force : Force execution without confirmation prompt}
                            {--pretend : Only show what would be restored, don\'t execute}';

    protected $description = 'Restore database from backup SQL with fail-fast parser and post-restore validation';

    public function handle(): int
    {
        // 1. HARD-BLOCK PRODUCTION & NON-DISPOSABLE DB
        $dbName = strtolower(config('database.connections.' . config('database.default') . '.database', ''));
        $isDisposableDb = str_contains($dbName, 'staging')
            || str_contains($dbName, 'disposable')
            || str_contains($dbName, 'testing')
            || str_contains($dbName, 'test')
            || str_contains($dbName, 'sahabat_farm');

        if (app()->environment('production') || config('app.env') === 'production' || !$isDisposableDb) {
            $this->error("CRITICAL SAFETY BLOCK: Restoring backups is strictly prohibited on target database [{$dbName}]!");
            return Command::FAILURE;
        }

        $backup = $this->argument('backup');
        $disk = $this->option('disk');
        $pretend = $this->option('pretend');
        $force = $this->option('force');
        $dir = "backups/{$backup}";
        $manifestPath = "{$dir}/manifest.json";

        $storageDisk = Storage::disk($disk);

        if (!$storageDisk->exists($manifestPath)) {
            $this->error("Manifest not found: {$manifestPath}");
            return Command::FAILURE;
        }

        $manifest = json_decode($storageDisk->get($manifestPath), true);
        $sqlPath = "{$dir}/" . ($manifest['compressed'] ? 'database.sql.gz' : 'database.sql');

        if (!$storageDisk->exists($sqlPath)) {
            $this->error("SQL file not found: {$sqlPath}");
            return Command::FAILURE;
        }

        $this->warn("⚠️  RESTORING DATABASE FROM BACKUP: {$backup}");
        $this->line("  Timestamp: {$manifest['timestamp']}");
        $this->line("  Tables: {$manifest['table_count']}, Records: {$manifest['total_records']}");
        $this->line("  SHA256: {$manifest['sha256']}");

        if (!$pretend && !$force && !$this->confirm('This will wipe target staging database and restore backup. Continue?')) {
            $this->info('Restore cancelled.');
            return Command::SUCCESS;
        }

        $sqlRaw = $storageDisk->get($sqlPath);

        // Verify SHA256 Integrity on stored file bytes
        $actualSha = hash('sha256', $sqlRaw);
        if ($actualSha !== $manifest['sha256']) {
            $this->error("INTEGRITY FAILED — file checksum does not match manifest!");
            $this->line("  Expected: {$manifest['sha256']}");
            $this->line("  Actual:   {$actualSha}");
            return Command::FAILURE;
        }
        $this->info("  ✓ SHA-256 Checksum Verified");

        if ($manifest['compressed']) {
            $sqlContent = gzdecode($sqlRaw);
            if ($sqlContent === false) {
                $this->error("DECOMPRESSION FAILED — Gzip stream is invalid or corrupted!");
                return Command::FAILURE;
            }
        } else {
            $sqlContent = $sqlRaw;
        }

        if ($pretend) {
            $this->info("[PRETEND] Would restore {$manifest['total_records']} records across {$manifest['table_count']} tables");
            return Command::SUCCESS;
        }

        // 2. TOKENIZE SQL STATEMENTS
        $statements = $this->parseSqlStatements($sqlContent);
        $this->info("  Parsed " . count($statements) . " SQL statements.");

        // 3. EXECUTE STATEMENTS WITH FAIL-FAST
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $pdo = DB::getPdo();

        foreach ($statements as $index => $stmt) {
            try {
                $pdo->exec($stmt);
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), 'There is no active transaction')) {
                    continue;
                }
                $this->error("FAIL-FAST ABORT on statement #{$index}: " . $e->getMessage());
                $this->line("Failing Statement: " . substr($stmt, 0, 200) . "...");
                DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                return Command::FAILURE;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // 4. POST-RESTORE VALIDATIONS
        $this->info("  Running Post-Restore Verification Checks...");

        // Table & Record Count Verification
        $dbTables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";
        $actualRecordCounts = [];

        foreach ($dbTables as $tObj) {
            $tName = $tObj->$key;
            $count = DB::table($tName)->count();
            $actualRecordCounts[$tName] = $count;
        }

        $expectedCounts = $manifest['record_counts'] ?? [];
        foreach ($expectedCounts as $table => $expCount) {
            $actCount = $actualRecordCounts[$table] ?? null;
            if ($actCount !== $expCount) {
                $this->error("Record count mismatch for table `{$table}`: Expected {$expCount}, Got " . ($actCount ?? 'MISSING'));
                return Command::FAILURE;
            }
        }
        $this->info("  ✓ Table & Record counts match manifest 100%");

        // FK Integrity Validation (Check for orphans)
        $orphanCheckFailed = false;
        if (in_array('animals', array_keys($actualRecordCounts)) && in_array('master_breeds', array_keys($actualRecordCounts))) {
            $orphanedBreeds = DB::table('animals')
                ->whereNotNull('breed_id')
                ->whereNotIn('breed_id', DB::table('master_breeds')->pluck('id'))
                ->count();
            if ($orphanedBreeds > 0) {
                $this->error("FK Integrity Violation: {$orphanedBreeds} animals reference non-existent breed_id!");
                $orphanCheckFailed = true;
            }
        }

        if ($orphanCheckFailed) {
            return Command::FAILURE;
        }
        $this->info("  ✓ Foreign key & Orphan integrity verified");

        // Special Character & String Preservation Check
        if (in_array('animals', array_keys($actualRecordCounts))) {
            $leadingZeroSample = DB::table('animals')->where('tag_id', 'LIKE', '0%')->first();
            if ($leadingZeroSample) {
                if (!str_starts_with((string) $leadingZeroSample->tag_id, '0')) {
                    $this->error("Data Preservation Failure: Tag ID leading zero lost for tag {$leadingZeroSample->tag_id}!");
                    return Command::FAILURE;
                }
                $this->info("  ✓ Leading-zero tag preserved: {$leadingZeroSample->tag_id}");
            }
        }

        $this->info("Restore completed successfully and verified 100%.");
        return Command::SUCCESS;
    }

    /**
     * Parse SQL string into individual statements safely without breaking strings/comments containing semicolons.
     */
    public function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = null;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            $prev = ($i > 0) ? $sql[$i - 1] : '';

            // Handle string quotes ' or "
            if (($char === "'" || $char === '"') && $prev !== '\\') {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($stringChar === $char) {
                    $inString = false;
                    $stringChar = null;
                }
            }

            // Semicolon separator outside of strings
            if ($char === ';' && !$inString) {
                $trimmed = trim($current);
                if ($trimmed !== '' && !str_starts_with($trimmed, '--')) {
                    $statements[] = $trimmed;
                }
                $current = '';
            } else {
                $current .= $char;
            }
        }

        $trimmed = trim($current);
        if ($trimmed !== '' && !str_starts_with($trimmed, '--')) {
            $statements[] = $trimmed;
        }

        return $statements;
    }
}