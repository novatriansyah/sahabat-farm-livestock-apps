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
        DB::statement("ALTER TABLE exit_logs MODIFY COLUMN exit_type ENUM('DEATH', 'SALE', 'SLAUGHTER') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Warning: This could fail if there are 'SLAUGHTER' records. This is just for schema rollback logic.
        DB::statement("ALTER TABLE exit_logs MODIFY COLUMN exit_type ENUM('DEATH', 'SALE') NOT NULL");
    }
};
