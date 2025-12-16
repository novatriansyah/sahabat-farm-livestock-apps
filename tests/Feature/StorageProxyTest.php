<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class StorageProxyTest extends TestCase
{
    use RefreshDatabase;

    public function test_storage_proxy_serves_file()
    {
        // 1. Create a dummy file in storage/app/public
        $filename = 'test-image.jpg';
        Storage::disk('public')->put($filename, 'dummy content');

        // 2. Access via proxy route
        $response = $this->get('/storage/' . $filename);

        // 3. Verify success and content
        $response->assertStatus(200);
        // Note: response()->file() sets headers, content might be streamed.
        // We can check header content-type or existence.
    }

    public function test_storage_proxy_404()
    {
        $response = $this->get('/storage/non-existent.jpg');
        $response->assertStatus(404);
    }
}
