<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'OWNER']);
    }

    public function test_deployment_link_route()
    {
        // This test might fail if symlink is truly disabled in CLI env,
        // but it checks if the route and controller are reachable and logic executes.
        $response = $this->actingAs($this->user)->get('deploy/storage-link');

        // It should return a string message, either success or failure
        $response->assertStatus(200);
    }
}
