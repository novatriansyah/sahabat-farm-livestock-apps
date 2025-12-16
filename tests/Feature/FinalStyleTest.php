<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterPartner;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinalStyleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'OWNER']);
    }

    public function test_report_page_loads_without_errors()
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);
        // Ensure no stray div issues (basic check if view renders)
    }

    public function test_partner_page_loads_without_errors()
    {
        $response = $this->actingAs($this->user)->get(route('partners.index'));
        $response->assertStatus(200);
    }
}
