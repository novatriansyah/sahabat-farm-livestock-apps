<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthorizationMatrixTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pemilik_can_access_canonical_export(): void
    {
        $pemilik = User::factory()->create(['role' => 'PEMILIK']);

        $response = $this->actingAs($pemilik)->get('/admin/export/animals');
        $response->assertStatus(200);
    }

    public function test_staf_cannot_access_canonical_export(): void
    {
        $staf = User::factory()->create(['role' => 'STAF']);

        $response = $this->actingAs($staf)->get('/admin/export/animals');
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_mitra_cannot_access_canonical_export(): void
    {
        $mitra = User::factory()->create(['role' => 'MITRA']);

        $response = $this->actingAs($mitra)->get('/admin/export/animals');
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }
}
