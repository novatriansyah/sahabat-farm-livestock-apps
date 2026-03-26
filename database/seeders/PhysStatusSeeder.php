<?php

namespace Database\Seeders;

use App\Models\MasterPhysStatus;
use Illuminate\Database\Seeder;

class PhysStatusSeeder extends Seeder
{
    public static $statuses = [
        'Cempe',
        'Bakalan (Jantan)',
        'Dara (Betina)',
        'Jantan siap kawin',
        'Betina siap kawin',
        'Bunting',
        'Menyusui',
        'Penggemukan - Siap Jual',
        'Karantina'
    ];

    public function run(): void
    {
        foreach (self::$statuses as $index => $status) {
            MasterPhysStatus::updateOrCreate(
                ['id' => $index + 1],
                ['name' => $status]
            );
        }

        // Cleanup any legacy IDs beyond the current list
        MasterPhysStatus::where('id', '>', count(self::$statuses))->delete();
    }
}
