<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupRestoreWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_backup_database_creates_valid_sql_and_verifies_sha256()
    {
        Storage::fake('local');

        $exitCode = Artisan::call('backup:database', ['--compress' => true]);
        $this->assertEquals(0, $exitCode);

        $directories = Storage::disk('local')->directories('backups');
        $this->assertNotEmpty($directories);

        $latestDir = basename($directories[0]);

        $verifyExit = Artisan::call('backup:verify', ['backup' => $latestDir]);
        $this->assertEquals(0, $verifyExit);
    }

    public function test_backup_media_creates_zero_media_evidence_when_no_media()
    {
        Storage::fake('local');

        $exitCode = Artisan::call('backup:media');
        $this->assertEquals(0, $exitCode);

        $directories = Storage::disk('local')->directories('backups');
        $this->assertNotEmpty($directories);

        $manifestPath = $directories[0] . '/manifest.json';
        $this->assertTrue(Storage::disk('local')->exists($manifestPath));

        $manifest = json_decode(Storage::disk('local')->get($manifestPath), true);
        $this->assertTrue($manifest['zero_media_evidence']);
    }

    protected function tearDown(): void
    {
        Storage::forgetDisk('local');
        parent::tearDown();
    }
}
