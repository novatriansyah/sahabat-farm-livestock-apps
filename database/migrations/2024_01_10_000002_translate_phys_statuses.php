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
        $translations = [
            'SUCKLING' => 'Cempe Lahir',
            'WEANED' => 'Cempe Sapih',
            'READY_TO_MATE' => 'Dara',
            'PREGNANT' => 'Bunting',
            'LACTATING' => 'Menyusui',
            'FATTENING' => 'Penggemukan - Siap Jual',
            'QUARANTINE' => 'Karantina',
        ];

        foreach ($translations as $oldName => $newName) {
            DB::table('master_phys_statuses')
                ->where('name', $oldName)
                ->update(['name' => $newName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $translations = [
            'Cempe Lahir' => 'SUCKLING',
            'Cempe Sapih' => 'WEANED',
            'Dara' => 'READY_TO_MATE',
            'Bunting' => 'PREGNANT',
            'Menyusui' => 'LACTATING',
            'Penggemukan - Siap Jual' => 'FATTENING',
            'Karantina' => 'QUARANTINE',
        ];

        foreach ($translations as $newName => $oldName) {
            DB::table('master_phys_statuses')
                ->where('name', $newName)
                ->update(['name' => $oldName]);
        }
    }
};
