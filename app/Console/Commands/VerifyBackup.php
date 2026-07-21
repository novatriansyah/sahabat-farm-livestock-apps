<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class VerifyBackup extends Command
{
    protected $signature = 'backup:verify
                            {backup : Backup directory name (e.g. 2026-07-21_16-42-06)}
                            {--disk=local : Disk where backup is stored}';
    protected $description = 'Verify backup integrity by re-calculating SHA256';

    public function handle(): int
    {
        $backup = $this->argument('backup');
        $disk = $this->option('disk');
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

        $this->info("Verifying backup: {$backup}");
        $this->line("  Expected SHA256: {$manifest['sha256']}");

        $sqlContent = Storage::disk($disk)->get($sqlPath);
        if ($manifest['compressed']) {
            $sqlContent = gzdecode($sqlContent);
        }

        $actualSha = hash('sha256', $sqlContent);
        $this->line("  Actual SHA256:   {$actualSha}");

        if ($actualSha === $manifest['sha256']) {
            $this->info("  ✓ INTEGRITY PASSED");
            $this->line("  Tables: {$manifest['table_count']}, Records: {$manifest['total_records']}");
            $this->line("  Timestamp: {$manifest['timestamp']}");
            return Command::SUCCESS;
        }

        $this->error("  ✗ INTEGRITY FAILED — file has been modified");
        return Command::FAILURE;
    }
}