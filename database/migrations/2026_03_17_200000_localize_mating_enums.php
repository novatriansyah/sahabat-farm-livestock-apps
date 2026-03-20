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
        // 1. mating_colonies: status
        DB::statement("ALTER TABLE mating_colonies MODIFY COLUMN status ENUM('ACTIVE', 'COMPLETED', 'AKTIF', 'SELESAI') DEFAULT 'AKTIF'");
        DB::table('mating_colonies')->where('status', 'ACTIVE')->update(['status' => 'AKTIF']);
        DB::table('mating_colonies')->where('status', 'COMPLETED')->update(['status' => 'SELESAI']);
        DB::statement("ALTER TABLE mating_colonies MODIFY COLUMN status ENUM('AKTIF', 'SELESAI') DEFAULT 'AKTIF'");

        // 2. mating_colony_members: status
        DB::statement("ALTER TABLE mating_colony_members MODIFY COLUMN status ENUM('MATING', 'PREGNANT', 'FAILED', 'NURSING', 'READY', 'KAWIN', 'BUNTING', 'GAGAL', 'MENYUSUI', 'SIAP') DEFAULT 'KAWIN'");
        DB::table('mating_colony_members')->where('status', 'MATING')->update(['status' => 'KAWIN']);
        DB::table('mating_colony_members')->where('status', 'PREGNANT')->update(['status' => 'BUNTING']);
        DB::table('mating_colony_members')->where('status', 'FAILED')->update(['status' => 'GAGAL']);
        DB::table('mating_colony_members')->where('status', 'NURSING')->update(['status' => 'MENYUSUI']);
        DB::table('mating_colony_members')->where('status', 'READY')->update(['status' => 'SIAP']);
        DB::statement("ALTER TABLE mating_colony_members MODIFY COLUMN status ENUM('KAWIN', 'BUNTING', 'GAGAL', 'MENYUSUI', 'SIAP') DEFAULT 'KAWIN'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. mating_colonies: status
        DB::statement("ALTER TABLE mating_colonies MODIFY COLUMN status ENUM('AKTIF', 'SELESAI', 'ACTIVE', 'COMPLETED') DEFAULT 'ACTIVE'");
        DB::table('mating_colonies')->where('status', 'AKTIF')->update(['status' => 'ACTIVE']);
        DB::table('mating_colonies')->where('status', 'SELESAI')->update(['status' => 'COMPLETED']);
        DB::statement("ALTER TABLE mating_colonies MODIFY COLUMN status ENUM('ACTIVE', 'COMPLETED') DEFAULT 'ACTIVE'");

        // 2. mating_colony_members: status
        DB::statement("ALTER TABLE mating_colony_members MODIFY COLUMN status ENUM('KAWIN', 'BUNTING', 'GAGAL', 'MENYUSUI', 'SIAP', 'MATING', 'PREGNANT', 'FAILED', 'NURSING', 'READY') DEFAULT 'MATING'");
        DB::table('mating_colony_members')->where('status', 'KAWIN')->update(['status' => 'MATING']);
        DB::table('mating_colony_members')->where('status', 'BUNTING')->update(['status' => 'PREGNANT']);
        DB::table('mating_colony_members')->where('status', 'GAGAL')->update(['status' => 'FAILED']);
        DB::table('mating_colony_members')->where('status', 'MENYUSUI')->update(['status' => 'NURSING']);
        DB::table('mating_colony_members')->where('status', 'SIAP')->update(['status' => 'READY']);
        DB::statement("ALTER TABLE mating_colony_members MODIFY COLUMN status ENUM('MATING', 'PREGNANT', 'FAILED', 'NURSING', 'READY') DEFAULT 'MATING'");
    }
};
