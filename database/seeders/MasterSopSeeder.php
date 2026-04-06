<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sops = [
            // KEDATANGAN (ARRIVAL)
            ['event_type' => 'ARRIVAL', 'title' => 'Berikan hijauan saja (tanpa konsentrat) selama 24 jam', 'task_type' => 'ARRIVAL', 'due_days_offset' => 0],
            ['event_type' => 'ARRIVAL', 'title' => 'Berikan minuman Gula Merah + Asam Jawa', 'task_type' => 'ARRIVAL', 'due_days_offset' => 0],
            ['event_type' => 'ARRIVAL', 'title' => 'Pengecekan Masuk Karantina', 'task_type' => 'ARRIVAL', 'due_days_offset' => 0],
            
            // KELAHIRAN (BIRTH)
            ['event_type' => 'BIRTH', 'title' => 'Pemberian Kolostrum (Wajib < 2 jam)', 'task_type' => 'HEALTH', 'due_days_offset' => 0],
            ['event_type' => 'BIRTH', 'title' => 'Desinfeksi Tali Pusar', 'task_type' => 'HEALTH', 'due_days_offset' => 0],
            ['event_type' => 'BIRTH', 'title' => 'Pengecekan Refleks Menyusu', 'task_type' => 'HEALTH', 'due_days_offset' => 0],
            ['event_type' => 'BIRTH', 'title' => 'Penimbangan Ulang (7 Hari)', 'task_type' => 'ROUTINE', 'due_days_offset' => 7],
        ];

        foreach ($sops as $sop) {
            \App\Models\MasterSop::updateOrCreate(
                ['event_type' => $sop['event_type'], 'title' => $sop['title']],
                $sop
            );
        }
    }
}
