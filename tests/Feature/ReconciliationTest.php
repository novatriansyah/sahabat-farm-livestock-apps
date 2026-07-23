<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Animal;
use App\Models\ReconciliationLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReconciliationTest extends TestCase
{
    use DatabaseTransactions;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->owner = User::factory()->create(['role' => 'PEMILIK']);
    }

    public function test_reconcile_requires_file()
    {
        $this->actingAs($this->owner)
            ->from('/admin/export/reconciliation')
            ->post('/admin/export/reconcile', [])
            ->assertSessionHasErrors('file');
    }

    public function test_reconcile_requires_xlsx()
    {
        $file = UploadedFile::fake()->create('data.csv', 100);
        $this->actingAs($this->owner)
            ->from('/admin/export/reconciliation')
            ->post('/admin/export/reconcile', ['file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_reconcile_index_shows_batches()
    {
        // Create a reconciliation log entry
        ReconciliationLog::create([
            'batch_id' => 'test-batch-123',
            'status' => 'SAME',
            'confidence' => 1.0,
        ]);

        $this->actingAs($this->owner)
            ->get('/admin/export/reconciliation')
            ->assertStatus(200)
            ->assertSee('test-batch-123');
    }

    public function test_reconcile_show_shows_batch_detail()
    {
        ReconciliationLog::create([
            'batch_id' => 'test-batch-456',
            'status' => 'CONFLICT',
            'tag_id' => 'B31',
            'field' => 'birth_date',
            'old_value' => '2025-11-24',
            'new_value' => '2025-11-23',
            'confidence' => 0.9,
        ]);

        $this->actingAs($this->owner)
            ->get('/admin/export/reconciliation/test-batch-456')
            ->assertStatus(200)
            ->assertSee('B31')
            ->assertSee('CONFLICT');
    }

    public function test_reconcile_show_404_for_unknown_batch()
    {
        // App redirects 404 to dashboard (see bootstrap/app.php exception handler)
        $this->actingAs($this->owner)
            ->get('/admin/export/reconciliation/unknown-batch')
            ->assertStatus(302);
    }

    public function test_apply_reconciliation_endpoint_no_longer_exists()
    {
        $this->actingAs($this->owner)
            ->post('/admin/export/apply-reconciliation')
            ->assertStatus(404);
    }
}