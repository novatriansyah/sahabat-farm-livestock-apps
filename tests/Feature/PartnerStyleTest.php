<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PartnerStyleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'OWNER']);
    }

    public function test_create_partner_page_renders()
    {
        $response = $this->actingAs($this->user)->get(route('partners.create'));
        $response->assertStatus(200);
    }
}
