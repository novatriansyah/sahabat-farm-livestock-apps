<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['key' => 'available_necklace_colors', 'value' => 'Merah, Biru, Hijau, Kuning, Hitam, Putih', 'label' => 'Daftar Warna Kalung (pisahkan dengan koma)', 'group' => 'OPERATIONAL'],
            ['key' => 'available_ear_tag_colors', 'value' => 'Merah, Biru, Hijau, Kuning, Hitam, Orange, Orange Persegi, Hijau Persegi, Kuning Orange', 'label' => 'Daftar Warna Ear Tag (pisahkan dengan koma)', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_dorper_f1', 'value' => 'Kuning', 'label' => 'Warna Ear Tag Dorper F1', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_dorper_f2', 'value' => 'Orange', 'label' => 'Warna Ear Tag Dorper F2', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_dorper_f3', 'value' => 'Kuning Orange', 'label' => 'Warna Ear Tag Dorper F3', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_dorper_f4', 'value' => 'Orange Persegi', 'label' => 'Warna Ear Tag Dorper F4', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_dorper_f5', 'value' => 'Hijau Persegi', 'label' => 'Warna Ear Tag Dorper F5', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_dorper_f6', 'value' => 'Kuning Orange', 'label' => 'Warna Ear Tag Dorper F6', 'group' => 'OPERATIONAL'],
            ['key' => 'eartag_map_default', 'value' => 'Hijau', 'label' => 'Warna Ear Tag Default (Ras Lain)', 'group' => 'OPERATIONAL'],
        ];

        foreach ($settings as $setting) {
            DB::table('farm_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'available_necklace_colors',
            'available_ear_tag_colors',
            'eartag_map_dorper_f1',
            'eartag_map_dorper_f2',
            'eartag_map_dorper_f3',
            'eartag_map_dorper_f4',
            'eartag_map_dorper_f5',
            'eartag_map_dorper_f6',
            'eartag_map_default',
        ];

        DB::table('farm_settings')->whereIn('key', $keys)->delete();
    }
};
