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

    public function test_backup_command_creates_sql_and_manifest(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::firstOrCreate(['name' => 'Garut', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang A'], ['type' => 'Koloni']);
        $status = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        Animal::create([
            'id' => '99999999-9999-9999-9999-999999999999',
            'tag_id' => '036',
            'owner_id' => $user->id,
            'category_id' => $category->id,
            'breed_id' => $breed->id,
            'current_location_id' => $location->id,
            'current_phys_status_id' => $status->id,
            'gender' => 'BETINA',
            'birth_date' => '2025-01-01',
            'acquisition_type' => 'BELI',
        ]);

        $exitCode = Artisan::call('backup:database', ['--disk' => 'local']);

        $this->assertEquals(0, $exitCode);

        $directories = Storage::disk('local')->directories('backups');
        $this->assertNotEmpty($directories);

        $latestBackupDir = end($directories);
        $this->assertTrue(Storage::disk('local')->exists("{$latestBackupDir}/database.sql"));
        $this->assertTrue(Storage::disk('local')->exists("{$latestBackupDir}/manifest.json"));
        $this->assertTrue(Storage::disk('local')->exists("{$latestBackupDir}/database.sql.sha256"));
    }

    public function test_restore_preserves_special_characters_and_record_counts(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $category = MasterCategory::firstOrCreate(['name' => 'Kambing']);
        $breed = MasterBreed::firstOrCreate(['name' => 'Garut', 'category_id' => $category->id]);
        $location = MasterLocation::firstOrCreate(['name' => 'Kandang A'], ['type' => 'Koloni']);
        $status = MasterPhysStatus::firstOrCreate(['name' => 'SEHAT']);

        Animal::create([
            'id' => '88888888-8888-8888-8888-888888888888',
            'tag_id' => '010',
            'owner_id' => $user->id,
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

        $output = Artisan::output();
        $this->assertEquals(0, $exitCode, "Restore output: {$output}");

        $restored = Animal::find('88888888-8888-8888-8888-888888888888');
        $this->assertNotNull($restored);
        $this->assertEquals('010', $restored->tag_id);
        $this->assertStringContainsString("Line 1\nLine 2 with ; semicolon", $restored->google_drive_link);
    }
}
