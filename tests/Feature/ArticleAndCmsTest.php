<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\FarmSetting;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArticleAndCmsTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'PEMILIK']);
    }

    public function test_guest_can_view_published_articles_index()
    {
        // Create a published and an unpublished article
        Article::create([
            'title' => 'Artikel Published',
            'summary' => 'Ringkasan published',
            'content' => '<p>Isi published</p>',
            'is_published' => true
        ]);

        Article::create([
            'title' => 'Artikel Draft',
            'summary' => 'Ringkasan draft',
            'content' => '<p>Isi draft</p>',
            'is_published' => false
        ]);

        $response = $this->get(route('pages.articles.index'));

        $response->assertStatus(200);
        $response->assertSee('Artikel Published');
        $response->assertDontSee('Artikel Draft');
    }

    public function test_guest_can_view_single_published_article()
    {
        $article = Article::create([
            'title' => 'Judul Artikel Tunggal',
            'summary' => 'Ringkasan tunggal',
            'content' => '<p>Isi artikel lengkap</p>',
            'is_published' => true
        ]);

        $response = $this->get(route('pages.articles.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Judul Artikel Tunggal');
        $response->assertSee('Isi artikel lengkap');
    }

    public function test_guest_cannot_view_unpublished_article()
    {
        $article = Article::create([
            'title' => 'Judul Artikel Draft',
            'summary' => 'Ringkasan draft',
            'content' => '<p>Isi artikel draft</p>',
            'is_published' => false
        ]);

        $response = $this->get(route('pages.articles.show', $article->slug));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_view_articles_index()
    {
        $article = Article::create([
            'title' => 'Artikel Admin Test',
            'summary' => 'Ringkasan admin',
            'content' => '<p>Isi admin</p>',
            'is_published' => true
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.articles.index'));

        $response->assertStatus(200);
        $response->assertSee('Artikel Admin Test');
    }

    public function test_admin_can_create_article()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('thumbnail.png', 10, 'image/png');

        $response = $this->actingAs($this->adminUser)->post(route('admin.articles.store'), [
            'title' => 'Artikel Baru Dari Test',
            'summary' => 'Ini ringkasan artikel baru',
            'content' => '<h1>Konten Baru</h1><p>Test</p>',
            'thumbnail_file' => $file,
            'is_published' => '1',
            'video_url' => 'https://www.youtube.com/watch?v=12345678901'
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        
        $article = Article::where('title', 'Artikel Baru Dari Test')->first();
        $this->assertNotNull($article);
        $this->assertEquals('artikel-baru-dari-test', $article->slug);
        $this->assertTrue($article->is_published);
        $this->assertNotNull($article->thumbnail);
        $this->assertEquals('https://www.youtube.com/watch?v=12345678901', $article->video_url);

        Storage::disk('public')->assertExists($article->thumbnail);
    }

    public function test_admin_can_update_article()
    {
        Storage::fake('public');
        $article = Article::create([
            'title' => 'Judul Awal',
            'summary' => 'Ringkasan awal',
            'content' => '<p>Konten awal</p>',
            'is_published' => false
        ]);

        $newFile = UploadedFile::fake()->create('new_thumbnail.png', 10, 'image/png');

        $response = $this->actingAs($this->adminUser)->put(route('admin.articles.update', $article->id), [
            'title' => 'Judul Diubah',
            'summary' => 'Ringkasan diubah',
            'content' => '<p>Konten diubah</p>',
            'thumbnail_file' => $newFile,
            'is_published' => '1'
        ]);

        $response->assertRedirect(route('admin.articles.index'));
        
        $article->refresh();
        $this->assertEquals('Judul Diubah', $article->title);
        $this->assertEquals('judul-diubah', $article->slug);
        $this->assertTrue($article->is_published);
        $this->assertNotNull($article->thumbnail);

        Storage::disk('public')->assertExists($article->thumbnail);
    }

    public function test_admin_can_delete_article()
    {
        Storage::fake('public');
        
        $thumbnailPath = 'articles/test_thumb.webp';
        Storage::disk('public')->put($thumbnailPath, 'fake content');

        $article = Article::create([
            'title' => 'Artikel Dihapus',
            'summary' => 'Ringkasan hapus',
            'content' => '<p>Konten hapus</p>',
            'thumbnail' => $thumbnailPath,
            'is_published' => true
        ]);

        Storage::disk('public')->assertExists($thumbnailPath);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.articles.destroy', $article->id));

        $response->assertRedirect(route('admin.articles.index'));
        $this->assertNull(Article::find($article->id));
        Storage::disk('public')->assertMissing($thumbnailPath);
    }

    public function test_admin_can_upload_media_quill()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('media.png', 10, 'image/png');

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.articles.upload-media'), [
            'image' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);
        
        $url = $response->json('url');
        $path = str_replace('/storage/', '', $url);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_update_site_settings_cms()
    {
        Storage::fake('public');
        $aboutImage = UploadedFile::fake()->create('about.png', 10, 'image/png');
        $team1Image = UploadedFile::fake()->create('team1.png', 10, 'image/png');

        $payload = [
            'whatsapp' => '08123456789',
            'hero' => [
                'badge' => 'Test Badge',
                'headline' => 'Test Headline',
                'headline_accent' => 'Test Accent',
                'subheadline' => 'Test Subheadline'
            ],
            'stats' => [
                ['number' => '100', 'label' => 'Label 1'],
                ['number' => '200', 'label' => 'Label 2'],
                ['number' => '300', 'label' => 'Label 3'],
                ['number' => '400', 'label' => 'Label 4']
            ],
            'features_header' => [
                'title' => 'Fitur Title',
                'subtitle' => 'Fitur Subtitle'
            ],
            'features' => [
                ['title' => 'F1', 'desc' => 'D1'],
                ['title' => 'F2', 'desc' => 'D2'],
                ['title' => 'F3', 'desc' => 'D3'],
                ['title' => 'F4', 'desc' => 'D4'],
                ['title' => 'F5', 'desc' => 'D5'],
                ['title' => 'F6', 'desc' => 'D6']
            ],
            'about' => [
                'heading' => 'About Heading',
                'paragraph' => 'About Paragraph',
                'checklist' => ['C1', 'C2', 'C3', 'C4']
            ],
            'about_image' => $aboutImage,
            'about_us' => [
                'heading' => 'About Us Heading',
                'subheading' => 'About Us Subheading',
                'vision_title' => 'Vision Title',
                'vision_text' => 'Vision Text',
                'mission_title' => 'Mission Title',
                'mission_checklist' => ['M1', 'M2', 'M3']
            ],
            'about_us_team_1' => $team1Image,
            'showcase' => [
                ['tab_title' => 'Showcase 1', 'media_type' => 'IMAGE'],
                ['tab_title' => 'Showcase 2', 'media_type' => 'IMAGE'],
                ['tab_title' => 'Showcase 3', 'media_type' => 'VIDEO']
            ],
            'testimonials' => [
                ['quote' => 'Q1', 'name' => 'N1', 'role' => 'R1'],
                ['quote' => 'Q2', 'name' => 'N2', 'role' => 'R2'],
                ['quote' => 'Q3', 'name' => 'N3', 'role' => 'R3']
            ],
            'cta' => [
                'headline' => 'CTA Headline',
                'subheadline' => 'CTA Subheadline'
            ],
            'footer_tagline' => 'Footer Tagline'
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.site-content.update'), $payload);

        $response->assertRedirect();
        
        $this->assertEquals('08123456789', FarmSetting::get('whatsapp_number'));
        
        $hero = FarmSetting::getJson('site_hero');
        $this->assertEquals('Test Badge', $hero['badge']);
        
        $about = FarmSetting::getJson('site_about');
        $this->assertEquals('About Heading', $about['heading']);
        $this->assertNotEmpty($about['image']);
        Storage::disk('public')->assertExists($about['image']);

        $aboutUs = FarmSetting::getJson('site_about_us');
        $this->assertEquals('About Us Heading', $aboutUs['heading']);
        $this->assertNotEmpty($aboutUs['images']['team_1']);
        Storage::disk('public')->assertExists($aboutUs['images']['team_1']);
    }
}
