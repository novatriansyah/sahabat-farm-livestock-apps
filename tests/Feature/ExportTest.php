<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(['role' => 'PEMILIK']);
    }

    public function test_unauthenticated_user_gets_redirected_to_login()
    {
        $this->get('/admin/export/animals')->assertRedirect('/login');
        $this->get('/admin/export/animals/template')->assertRedirect('/login');
        $this->get('/admin/export/data-snapshot-json')->assertRedirect('/login');
    }

    public function test_non_pemilik_gets_redirected()
    {
        $mitra = User::factory()->create(['role' => 'MITRA']);
        $this->actingAs($mitra)
            ->get('/admin/export/animals')
            ->assertStatus(302);
    }

    public function test_export_animals_returns_excel_file()
    {
        $this->actingAs($this->owner)
            ->get('/admin/export/animals')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_template_returns_excel_file()
    {
        $this->actingAs($this->owner)
            ->get('/admin/export/animals/template')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_full_backup_returns_json()
    {
        $this->actingAs($this->owner)
            ->get('/admin/export/data-snapshot-json')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_full_backup_has_required_keys()
    {
        $response = $this->actingAs($this->owner)
            ->get('/admin/export/data-snapshot-json');

        $data = $response->json();
        $this->assertArrayHasKey('animals', $data);
        $this->assertArrayHasKey('weight_logs', $data);
        $this->assertArrayHasKey('farm_settings', $data);
        $this->assertArrayHasKey('exported_at', $data);
    }
}