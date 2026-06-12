<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create articles table
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('thumbnail')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 2. Seed new settings keys into farm_settings
        $newContent = [
            [
                'key' => 'site_about_us',
                'value' => json_encode([
                    'heading' => 'Tentang Sahabat Farm Indonesia',
                    'subheading' => 'Membangun masa depan peternakan Indonesia yang modern, efisien, dan berkelanjutan.',
                    'vision_title' => 'Visi Kami',
                    'vision_text' => 'Menjadi platform manajemen peternakan nomor satu di Asia Tenggara yang memberdayakan peternak kecil hingga skala industri.',
                    'mission_title' => 'Misi Kami',
                    'mission_checklist' => [
                        'Menyediakan teknologi yang mudah digunakan oleh seluruh lapisan peternak.',
                        'Meningkatkan akurasi data untuk mengoptimalkan profitabilitas peternak.',
                        'Memfasilitasi ekosistem peternakan yang transparan dan akuntabel.'
                    ],
                    'images' => [
                        'about_main' => '',
                        'team_1' => '',
                        'team_2' => '',
                        'team_3' => '',
                        'team_4' => ''
                    ]
                ]),
                'label' => 'Konten Halaman Tentang Kami',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_hero_showcase',
                'value' => json_encode([
                    [
                        'tab_title' => 'Dasbor Utama',
                        'media_type' => 'IMAGE',
                        'path' => 'img/dashboard_preview.png'
                    ],
                    [
                        'tab_title' => 'Breeding Cycle',
                        'media_type' => 'IMAGE',
                        'path' => 'img/dashboard_preview.png'
                    ],
                    [
                        'tab_title' => 'Video Walkthrough',
                        'media_type' => 'VIDEO',
                        'path' => ''
                    ]
                ]),
                'label' => 'Showcase Media Hero',
                'group' => 'SITE_CONTENT',
            ]
        ];

        foreach ($newContent as $item) {
            DB::table('farm_settings')->updateOrInsert(
                ['key' => $item['key']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
        DB::table('farm_settings')->whereIn('key', ['site_about_us', 'site_hero_showcase'])->delete();
    }
};
