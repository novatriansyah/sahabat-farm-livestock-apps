<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPhysStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupRestoreCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_backup_creates_streaming_sql_and_manifest_with_sha256(): void
    {
        $owner = User::factory()->create(['role' => 'PEMILIK']);
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::create(['name' => 'TEXEL', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang A', 'type' => 'Koloni']);
        $status = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        Animal::create([
            'id' => '99999999-9999-9999-9999-999999999999',
            'tag_id' => '036',
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $status->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'google_drive_link' => "https://drive.google.com/test\nWith semicolon; and 'quotes' & Unicode \u{2605}",
        ]);

        $exitCode = Artisan::call('backup:database', ['--disk' => 'local']);
        $this->assertEquals(0, $exitCode);

        $directories = Storage::disk('local')->directories('backups');
        $this->assertNotEmpty($directories);
        $latestBackup = end($directories);

        $manifestPath = "{$latestBackup}/manifest.json";
        $sqlPath = "{$latestBackup}/database.sql";

        $this->assertTrue(Storage::disk('local')->exists($manifestPath));
        $this->assertTrue(Storage::disk('local')->exists($sqlPath));

        $manifest = json_decode(Storage::disk('local')->get($manifestPath), true);
        $this->assertArrayHasKey('sha256', $manifest);
        $this->assertArrayHasKey('record_counts', $manifest);
        $this->assertArrayHasKey('commit_hash', $manifest);

        $actualSha = hash('sha256', Storage::disk('local')->get($sqlPath));
        $this->assertEquals($manifest['sha256'], $actualSha);
    }

    public function test_restore_hard_blocks_in_production_environment(): void
    {
        $this->app['env'] = 'production';

        $exitCode = Artisan::call('backup:restore', ['backup' => 'dummy-backup', '--force' => true]);
        $this->assertEquals(1, $exitCode);
    }

    public function test_restore_preserves_special_characters_and_record_counts(): void
    {
        $owner = User::factory()->create(['role' => 'PEMILIK']);
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::create(['name' => 'MERINO', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang A', 'type' => 'Koloni']);
        $status = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        Animal::create([
            'id' => '88888888-8888-8888-8888-888888888888',
            'tag_id' => '010',
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $status->id,
            'gender' => 'JANTAN',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'HASIL_TERNAK',
            'google_drive_link' => "Line 1\nLine 2 with ; semicolon",
        ]);

        Artisan::call('backup:database', ['--disk' => 'local']);
        $directories = Storage::disk('local')->directories('backups');
        $latestBackupDir = basename(end($directories));

        $exitCode = Artisan::call('backup:restore', [
            'backup' => $latestBackupDir,
            '--disk' => 'local',
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);

        $restored = Animal::find('88888888-8888-8888-8888-888888888888');
        $this->assertNotNull($restored);
        $this->assertEquals('010', $restored->tag_id);
        $this->assertStringContainsString("Line 1\nLine 2 with ; semicolon", $restored->google_drive_link);
    }
}
