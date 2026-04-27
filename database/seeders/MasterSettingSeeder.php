<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // OPERATIONAL
            ['key' => 'gestation_period_days', 'value' => '150', 'label' => 'Masa Bunting (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'nifas_period_days', 'value' => '40', 'label' => 'Masa Nifas/Pemulihan (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'weaning_age_days', 'value' => '35', 'label' => 'Usia Sapih (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'pregnancy_check_days', 'value' => '60', 'label' => 'Jadwal Cek Kebuntingan (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'separation_age_days', 'value' => '60', 'label' => 'Jadwal Pemisahan Cempe (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'kid_threshold_days', 'value' => '40', 'label' => 'Ambang Batas Usia Cempe (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'low_stock_threshold', 'value' => '10', 'label' => 'Ambang Batas Stok Rendah', 'group' => 'OPERATIONAL'],
            ['key' => 'default_invoice_due_days', 'value' => '7', 'label' => 'Jatuh Tempo Invoice (Hari)', 'group' => 'FINANCIAL'],
            ['key' => 'min_age_mate_months_fallback', 'value' => '8', 'label' => 'Minimal Usia Kawin (Bulan) - Default', 'group' => 'OPERATIONAL'],
            ['key' => 'min_weight_mate_fallback', 'value' => '30', 'label' => 'Minimal Berat Kawin (Kg) - Default', 'group' => 'OPERATIONAL'],

            // FINANCIAL ESTIMATES (Fallback)
            ['key' => 'est_feed_cost_day', 'value' => '5000', 'label' => 'Estimasi Biaya Pakan/Ekor/Hari', 'group' => 'FINANCIAL'],
            ['key' => 'est_health_cost_month', 'value' => '10000', 'label' => 'Estimasi Biaya Kesehatan/Ekor/Bulan', 'group' => 'FINANCIAL'],
            ['key' => 'est_ops_cost_month', 'value' => '15000', 'label' => 'Estimasi Biaya Operasional/Ekor/Bulan', 'group' => 'FINANCIAL'],
            ['key' => 'vaccine_alert_days', 'value' => '14', 'label' => 'Ambang Batas Alert Vaksin (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'mating_colony_days', 'value' => '60', 'label' => 'Durasi Koloni Kawin (Hari)', 'group' => 'OPERATIONAL'],
            ['key' => 'adg_performance_threshold', 'value' => '0.15', 'label' => 'Ambang Batas Performa ADG Bagus (Kg)', 'group' => 'OPERATIONAL'],
        ];

        foreach ($settings as $setting) {
            \App\Models\FarmSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Update Physical Status Flags
        \App\Models\MasterPhysStatus::where('name', 'like', '%Bunting%')->update(['is_breedable' => false, 'is_pregnant' => true]);
        \App\Models\MasterPhysStatus::where('name', 'like', '%Karantina%')->update(['is_breedable' => false, 'is_quarantine' => true]);
        \App\Models\MasterPhysStatus::where('name', 'like', '%Sakit%')->update(['is_breedable' => false, 'is_quarantine' => true]);
        \App\Models\MasterPhysStatus::where('name', 'like', '%Menyusui%')->update(['is_lactating' => true]);

        // Map Default Status IDs to Settings for robust referencing (Senior Feedback)
        $statusMapping = [
            'status_id_cempe' => \App\Models\MasterPhysStatus::where('name', 'like', '%Cempe%')->value('id'),
            'status_id_bakalan' => \App\Models\MasterPhysStatus::where('name', 'like', '%Bakalan%')->value('id'),
            'status_id_dara' => \App\Models\MasterPhysStatus::where('name', 'like', '%Dara%')->value('id'),
            'status_id_jantan_siap' => \App\Models\MasterPhysStatus::where('name', 'like', '%Jantan siap%')->value('id'),
            'status_id_betina_siap' => \App\Models\MasterPhysStatus::where('name', 'like', '%Betina siap%')->value('id'),
            'status_id_menyusui' => \App\Models\MasterPhysStatus::where('is_lactating', true)->value('id'),
        ];

        foreach ($statusMapping as $key => $id) {
            if ($id) {
                \App\Models\FarmSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $id, 'label' => "ID Status $key", 'group' => 'SYSTEM']
                );
            }
        }
    }
}
