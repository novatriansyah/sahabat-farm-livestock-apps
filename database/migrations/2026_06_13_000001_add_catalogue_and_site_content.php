<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add catalogue fields to animals table
        Schema::table('animals', function (Blueprint $table) {
            $table->boolean('is_for_sale')->default(false)->after('google_drive_link');
            $table->decimal('sale_price', 15, 2)->nullable()->after('is_for_sale');
            $table->text('sale_description')->nullable()->after('sale_price');
        });

        // 2. Seed site content settings into farm_settings
        $siteContent = [
            [
                'key' => 'whatsapp_number',
                'value' => '',
                'label' => 'Nomor WhatsApp (format: 628xxx)',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_hero',
                'value' => json_encode([
                    'badge' => 'The Future of Livestock Management',
                    'headline' => 'Kelola Peternakan',
                    'headline_accent' => 'Jauh Lebih Cerdas',
                    'subheadline' => 'Modernisasi operasional peternakan Anda dengan platform data-driven terlengkap. Mulai dari pemantauan kesehatan hingga analitik finansial.',
                ]),
                'label' => 'Konten Hero Section',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_stats',
                'value' => json_encode([
                    ['number' => '500+', 'label' => 'Peternak Terdaftar'],
                    ['number' => '10K+', 'label' => 'Ternak Dikelola'],
                    ['number' => '25+', 'label' => 'Kota Wilayah Kerja'],
                    ['number' => '99%', 'label' => 'Akurasi Data'],
                ]),
                'label' => 'Statistik Angka',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_features_header',
                'value' => json_encode([
                    'title' => 'Powerfull fitur untuk mengelola bisnis Anda',
                    'subtitle' => 'Didesain dari nol untuk kebutuhan spesifik peternak di Indonesia.',
                ]),
                'label' => 'Header Section Fitur',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_features',
                'value' => json_encode([
                    ['title' => 'Profil Hewan Digital', 'desc' => 'Setiap ekor ternak memiliki kartu identitas digital lengkap dengan silsilah, vaksinasi, dan riwayat mutasi.'],
                    ['title' => 'Smart Breeding Tracking', 'desc' => 'Jangan pernah melewatkan masa subur. Sistem kami secara otomatis menghitung masa kebuntingan dan hari perkiraan lahir.'],
                    ['title' => 'Laporan Akuntansi Otomatis', 'desc' => 'Hasilkan laporan laba rugi, HPP, dan valuasi stok pakan secara instan tanpa perlu keahlian akuntansi mendalam.'],
                    ['title' => 'Scan QR Inovatif', 'desc' => 'Akses cepat data hewan hanya dengan scan QR Code. Mendukung upload dari galeri dan integrasi kamera mobile.'],
                    ['title' => 'Manajemen Pakan & Stok', 'desc' => 'Kelola gudang pakan dengan sistem First-In-First-Out (FIFO) dan pantau sisa stok secara real-time.'],
                    ['title' => 'Health Monitoring', 'desc' => 'Catat riwayat penyakit, pemberian vitamin, dan jadwal vaksinasi untuk menjaga kesehatan seluruh koloni.'],
                ]),
                'label' => 'Kartu Fitur (6 item)',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_about',
                'value' => json_encode([
                    'heading' => 'Siap Membawa Revolusi Digital ke Kandang Anda?',
                    'paragraph' => 'Sahabat Farm Indonesia lahir dari semangat untuk membantu peternak lokal bersaing di era digital. Kami mengombinasikan kearifan lokal peternakan dengan teknologi cloud terbaru untuk hasil maksimal.',
                    'checklist' => [
                        'Terintegrasi dengan sistem IoT (Dalam Pengembangan)',
                        'Data tersimpan aman di infrastruktur cloud terpercaya',
                        'Tim support ahli yang siap membantu implementasi',
                        'User interface ramah pengguna, bahkan untuk pemula',
                    ],
                ]),
                'label' => 'Konten Tentang Kami',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_testimonials',
                'value' => json_encode([
                    ['quote' => 'Setelah menggunakan Sahabat Farm Indonesia, saya bisa memantau pertumbuhan kambing hanya dari handphone. Sangat membantu untuk efisiensi pakan!', 'name' => 'Mitra Peternak 1', 'role' => 'Owner Farm Maju Jaya'],
                    ['quote' => 'Setelah menggunakan Sahabat Farm Indonesia, saya bisa memantau pertumbuhan kambing hanya dari handphone. Sangat membantu untuk efisiensi pakan!', 'name' => 'Mitra Peternak 2', 'role' => 'Owner Farm Maju Jaya'],
                    ['quote' => 'Setelah menggunakan Sahabat Farm Indonesia, saya bisa memantau pertumbuhan kambing hanya dari handphone. Sangat membantu untuk efisiensi pakan!', 'name' => 'Mitra Peternak 3', 'role' => 'Owner Farm Maju Jaya'],
                ]),
                'label' => 'Testimoni (3 slot)',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_cta',
                'value' => json_encode([
                    'headline' => 'Mulai Transformasi Peternakan Anda Hari Ini',
                    'subheadline' => 'Bergabunglah dengan ratusan peternak lainnya yang telah mendigitalisasi bisnis mereka. Gratis konsultasi awal!',
                ]),
                'label' => 'Call to Action Section',
                'group' => 'SITE_CONTENT',
            ],
            [
                'key' => 'site_footer_tagline',
                'value' => 'Membangun ekosistem peternakan yang berkelanjutan dan modern melalui inovasi teknologi yang merakyat.',
                'label' => 'Tagline Footer',
                'group' => 'SITE_CONTENT',
            ],
        ];

        foreach ($siteContent as $item) {
            DB::table('farm_settings')->updateOrInsert(
                ['key' => $item['key']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn(['is_for_sale', 'sale_price', 'sale_description']);
        });

        DB::table('farm_settings')->where('group', 'SITE_CONTENT')->delete();
    }
};
