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
                            {--pretend : Only show what would be restored, don\'t execute}';
    protected $description = 'Restore database from a backup SQL file';

    public function handle(): int
    {
        $backup = $this->argument('backup');
        $disk = $this->option('disk');
        $pretend = $this->option('pretend');
        $dir = "backups/{$backup}";
        $manifestPath = "{$dir}/manifest.json";

        if (!Storage::disk($disk)->exists($manifestPath)) {
            $this->error("Manifest not found: {$manifestPath}");
            return Command::FAILURE;
        }

        $manifest = json_decode(Storage::disk($disk)->get($manifestPath), true);
        $sqlPath = "{$dir}/" . ($manifest['compressed'] ? 'database.sql.gz' : 'database.sql');

        if (!Storage::disk($disk)->exists($sqlPath)) {
            $this->error("SQL file not found: {$sqlPath}");
            return Command::FAILURE;
        }

        $this->warn("⚠️  RESTORING DATABASE FROM BACKUP: {$backup}");
        $this->line("  Timestamp: {$manifest['timestamp']}");
        $this->line("  Tables: {$manifest['table_count']}, Records: {$manifest['total_records']}");
        $this->line("  SHA256: {$manifest['sha256']}");

        if (!$pretend && !$this->confirm('This will DESTROY current data. Continue?')) {
            $this->info('Restore cancelled.');
            return Command::SUCCESS;
        }

        $sqlContent = Storage::disk($disk)->get($sqlPath);
        if ($manifest['compressed']) {
            $sqlContent = gzdecode($sqlContent);
        }

        // Verify integrity
        $actualSha = hash('sha256', $sqlContent);
        if ($actualSha !== $manifest['sha256']) {
            $this->error("INTEGRITY FAILED — file has been modified");
            $this->line("  Expected: {$manifest['sha256']}");
            $this->line("  Actual:   {$actualSha}");
            return Command::FAILURE;
        }
        $this->info("  ✓ Integrity verified");

        if ($pretend) {
            $this->info("[PRETEND] Would restore {$manifest['total_records']} records across {$manifest['table_count']} tables");
            $this->info("[PRETEND] SQL size: " . strlen($sqlContent) . " bytes");
            return Command::SUCCESS;
        }

        // Disable foreign key checks and restore
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Split by statement and execute
        $statements = explode(";\n", $sqlContent);
        $count = 0;
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (empty($stmt)) continue;
            try {
                DB::unprepared($stmt);
                $count++;
            } catch (\Exception $e) {
                $this->warn("  Statement {$count} failed: " . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        $this->info("Restore complete. Executed {$count} statements.");
        return Command::SUCCESS;
    }
}