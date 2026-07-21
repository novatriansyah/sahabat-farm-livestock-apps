<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupCommandTest extends TestCase
{
    public function test_backup_database_creates_sql_and_manifest()
    {
        Storage::fake('local');

        $this->artisan('backup:database', ['--disk' => 'local'])
            ->assertExitCode(0);

        $files = Storage::disk('local')->allFiles('backups');
        $this->assertNotEmpty($files);

        $hasSql = false;
        $hasManifest = false;
        foreach ($files as $f) {
            if (str_ends_with($f, 'database.sql')) $hasSql = true;
            if (str_ends_with($f, 'manifest.json')) $hasManifest = true;
        }
        $this->assertTrue($hasSql, 'SQL file not found');
        $this->assertTrue($hasManifest, 'Manifest not found');
    }

    public function test_backup_list_shows_backups()
    {
        Storage::fake('local');

        $this->artisan('backup:database', ['--disk' => 'local']);
        $this->artisan('backup:list', ['--disk' => 'local'])
            ->assertExitCode(0)
            ->expectsOutputToContain('Backup');
    }

    public function test_backup_verify_passes_for_valid_backup()
    {
        Storage::fake('local');

        $this->artisan('backup:database', ['--disk' => 'local']);

        $files = Storage::disk('local')->directories('backups');
        $this->assertNotEmpty($files);
        $backupDir = basename($files[0]);

        $this->artisan('backup:verify', ['backup' => $backupDir, '--disk' => 'local'])
            ->assertExitCode(0)
            ->expectsOutputToContain('INTEGRITY PASSED');
    }

    public function test_backup_restore_pretend_works_without_modifying_db()
    {
        Storage::fake('local');

        $this->artisan('backup:database', ['--disk' => 'local']);

        $files = Storage::disk('local')->directories('backups');
        $this->assertNotEmpty($files);
        $backupDir = basename($files[0]);

        $this->artisan('backup:restore', ['backup' => $backupDir, '--disk' => 'local', '--pretend' => true])
            ->assertExitCode(0)
            ->expectsOutputToContain('PRETEND');
    }

    public function test_backup_verify_fails_for_corrupted_backup()
    {
        Storage::fake('local');

        $this->artisan('backup:database', ['--disk' => 'local']);

        $files = Storage::disk('local')->allFiles('backups');
        $sqlFile = collect($files)->firstWhere(fn($f) => str_ends_with($f, 'database.sql'));

        // Corrupt the file
        Storage::disk('local')->put($sqlFile, 'CORRUPTED DATA');

        $backupDir = basename(dirname($sqlFile));

        $this->artisan('backup:verify', ['backup' => $backupDir, '--disk' => 'local'])
            ->assertExitCode(1);
    }

    public function test_backup_verify_fails_for_missing_backup()
    {
        $this->artisan('backup:verify', ['backup' => 'non-existent-backup', '--disk' => 'local'])
            ->assertExitCode(1);
    }
}