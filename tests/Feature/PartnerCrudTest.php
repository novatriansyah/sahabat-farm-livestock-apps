<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterPartner;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PartnerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'OWNER']);
    }

    public function test_can_view_partner_detail()
    {
        $partner = MasterPartner::create(['name' => 'Mitra Detail', 'contact_info' => '08123']);

        $response = $this->actingAs($this->user)->get(route('partners.show', $partner->id));

        $response->assertStatus(200);
        $response->assertSee('Mitra Detail');
        $response->assertSee('08123');
    }

    public function test_can_view_partner_index_with_links()
    {
        $partner = MasterPartner::create(['name' => 'Mitra Link', 'contact_info' => '08123']);

        $response = $this->actingAs($this->user)->get(route('partners.index'));

        $response->assertStatus(200);
        // Verify Detail, Edit, and Delete (Form) exist
        $response->assertSee(route('partners.show', $partner->id));
        $response->assertSee(route('partners.edit', $partner->id));
        $response->assertSee(route('partners.destroy', $partner->id));
    }
}
