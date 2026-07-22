<?php

namespace App\Http\Controllers;

use App\Models\FarmSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class SiteContentController extends Controller
{
    public function index(): View
    {
        $hero = FarmSetting::getJson('site_hero', [
            'badge' => '', 'headline' => '', 'headline_accent' => '', 'subheadline' => '',
        ]);
        $stats = FarmSetting::getJson('site_stats', [
            ['number' => '', 'label' => ''],
            ['number' => '', 'label' => ''],
            ['number' => '', 'label' => ''],
            ['number' => '', 'label' => ''],
        ]);
        $featuresHeader = FarmSetting::getJson('site_features_header', [
            'title' => '', 'subtitle' => '',
        ]);
        $features = FarmSetting::getJson('site_features', array_fill(0, 6, ['title' => '', 'desc' => '']));
        
        $about = FarmSetting::getJson('site_about', [
            'heading' => '', 'paragraph' => '', 'image' => '', 'checklist' => ['', '', '', ''],
        ]);
        
        $aboutUs = FarmSetting::getJson('site_about_us', [
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
                'team_1' => '', 'team_2' => '', 'team_3' => '', 'team_4' => ''
            ]
        ]);

        $showcase = FarmSetting::getJson('site_hero_showcase', [
            ['tab_title' => 'Dasbor Utama', 'media_type' => 'IMAGE', 'path' => 'img/dashboard_preview.png'],
            ['tab_title' => 'Breeding Cycle', 'media_type' => 'IMAGE', 'path' => 'img/dashboard_preview.png'],
            ['tab_title' => 'Video Walkthrough', 'media_type' => 'VIDEO', 'path' => '']
        ]);

        $testimonials = FarmSetting::getJson('site_testimonials', array_fill(0, 3, ['quote' => '', 'name' => '', 'role' => '']));
        $cta = FarmSetting::getJson('site_cta', ['headline' => '', 'subheadline' => '']);
        $footerTagline = FarmSetting::get('site_footer_tagline', '');
        $whatsapp = FarmSetting::get('whatsapp_number', '');

        // Feature icons/routes for display reference (read-only in form)
        $featureRefs = [
            ['icon' => 'Profil Hewan', 'route' => 'pages.digital-livestock'],
            ['icon' => 'Breeding', 'route' => '#'],
            ['icon' => 'Akuntansi', 'route' => 'pages.sales-tracking'],
            ['icon' => 'QR Scan', 'route' => '#'],
            ['icon' => 'Pakan & Stok', 'route' => 'pages.feed-management'],
            ['icon' => 'Health', 'route' => 'pages.health-monitoring'],
        ];

        return view('admin.site-content.index', compact(
            'hero', 'stats', 'featuresHeader', 'features', 'about', 'aboutUs', 'showcase',
            'testimonials', 'cta', 'footerTagline', 'whatsapp', 'featureRefs'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp' => 'nullable|string|max:20',
            'hero.badge' => 'required|string|max:100',
            'hero.headline' => 'required|string|max:100',
            'hero.headline_accent' => 'required|string|max:100',
            'hero.subheadline' => 'required|string|max:500',
            'stats' => 'required|array|size:4',
            'stats.*.number' => 'required|string|max:20',
            'stats.*.label' => 'required|string|max:50',
            'features_header.title' => 'required|string|max:200',
            'features_header.subtitle' => 'required|string|max:200',
            'features' => 'required|array|size:6',
            'features.*.title' => 'required|string|max:100',
            'features.*.desc' => 'required|string|max:500',
            
            // About (Landing Page)
            'about.heading' => 'required|string|max:300',
            'about.paragraph' => 'required|string|max:1000',
            'about.checklist' => 'required|array|size:4',
            'about.checklist.*' => 'required|string|max:200',
            'about_image' => 'nullable|image|max:5120',

            // About Us Page
            'about_us.heading' => 'required|string|max:300',
            'about_us.subheading' => 'required|string|max:500',
            'about_us.vision_title' => 'required|string|max:200',
            'about_us.vision_text' => 'required|string|max:1000',
            'about_us.mission_title' => 'required|string|max:200',
            'about_us.mission_checklist' => 'required|array|size:3',
            'about_us.mission_checklist.*' => 'required|string|max:300',
            'about_us_team_1' => 'nullable|image|max:5120',
            'about_us_team_2' => 'nullable|image|max:5120',
            'about_us_team_3' => 'nullable|image|max:5120',
            'about_us_team_4' => 'nullable|image|max:5120',

            // Showcase
            'showcase' => 'required|array',
            'showcase.*.tab_title' => 'required|string|max:100',
            'showcase.*.media_type' => 'required|in:IMAGE,VIDEO',
            'showcase_files' => 'nullable|array',
            'showcase_files.*' => 'nullable|file|max:25600',

            // Testimonials
            'testimonials' => 'required|array|size:3',
            'testimonials.*.quote' => 'required|string|max:500',
            'testimonials.*.name' => 'required|string|max:100',
            'testimonials.*.role' => 'required|string|max:100',
            'cta.headline' => 'required|string|max:200',
            'cta.subheadline' => 'required|string|max:500',
            'footer_tagline' => 'required|string|max:500',
        ]);

        // Process landing about image
        $aboutData = $validated['about'];
        $aboutExisting = FarmSetting::getJson('site_about', []);
        $aboutData['image'] = $aboutExisting['image'] ?? '';
        if ($request->hasFile('about_image')) {
            $aboutData['image'] = $this->uploadAndCompress($request->file('about_image'), 'site-content');
        }
        FarmSetting::set('site_about', $aboutData, 'Konten Tentang Kami (Home)', 'SITE_CONTENT');

        // Process About Us Page Content & Images
        $aboutUsData = $validated['about_us'];
        $aboutUsExisting = FarmSetting::getJson('site_about_us', []);
        $aboutUsData['images'] = $aboutUsExisting['images'] ?? [
            'team_1' => '', 'team_2' => '', 'team_3' => '', 'team_4' => ''
        ];
        for ($t = 1; $t <= 4; $t++) {
            if ($request->hasFile("about_us_team_$t")) {
                $aboutUsData['images']["team_$t"] = $this->uploadAndCompress($request->file("about_us_team_$t"), 'site-content');
            }
        }
        FarmSetting::set('site_about_us', $aboutUsData, 'Konten Halaman Tentang Kami', 'SITE_CONTENT');

        // Process Hero Showcase
        $showcaseData = $validated['showcase'];
        $showcaseExisting = FarmSetting::getJson('site_hero_showcase', []);
        foreach ($showcaseData as $index => &$item) {
            $item['path'] = $showcaseExisting[$index]['path'] ?? '';
            if ($request->hasFile("showcase_files.$index")) {
                $file = $request->file("showcase_files.$index");
                if ($item['media_type'] === 'VIDEO') {
                    $filename = 'site-content/' . uniqid() . '.' . $file->getClientOriginalExtension();
                    Storage::disk('public')->put($filename, file_get_contents($file));
                    $item['path'] = $filename;
                } else {
                    $item['path'] = $this->uploadAndCompress($file, 'site-content');
                }
            }
        }
        FarmSetting::set('site_hero_showcase', $showcaseData, 'Showcase Media Hero', 'SITE_CONTENT');

        // Update remaining text sections
        FarmSetting::set('whatsapp_number', $validated['whatsapp'] ?? '', 'Nomor WhatsApp', 'SITE_CONTENT');
        FarmSetting::set('site_hero', $validated['hero'], 'Konten Hero Section', 'SITE_CONTENT');
        FarmSetting::set('site_stats', $validated['stats'], 'Statistik Angka', 'SITE_CONTENT');
        FarmSetting::set('site_features_header', $validated['features_header'], 'Header Section Fitur', 'SITE_CONTENT');
        FarmSetting::set('site_features', $validated['features'], 'Kartu Fitur', 'SITE_CONTENT');
        FarmSetting::set('site_testimonials', $validated['testimonials'], 'Testimoni', 'SITE_CONTENT');
        FarmSetting::set('site_cta', $validated['cta'], 'Call to Action', 'SITE_CONTENT');
        FarmSetting::set('site_footer_tagline', $validated['footer_tagline'], 'Tagline Footer', 'SITE_CONTENT');

        return back()->with('success', 'Konten website berhasil diperbarui.');
    }

    private function uploadAndCompress($file, string $folder): string
    {
        $filename = $folder . '/' . uniqid() . '.webp';
        try {
            $image = Image::read($file);
            $image->scale(width: 1200);
            $encoded = $image->toWebp(75);
            Storage::disk('public')->put($filename, (string) $encoded);
        } catch (\Throwable $e) {
            $filename = $file->store($folder, 'public');
        }
        return $filename;
    }
}
