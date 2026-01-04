<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw statement for ENUM modification (MySQL specific but standard for this stack)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER', 'BREEDER', 'STAFF', 'PARTNER') NOT NULL DEFAULT 'STAFF'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original
        // Note: This might fail if there are 'PARTNER' rows, so usually we don't strict revert data-loss changes, 
        // but for schema correctness logic:
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER', 'BREEDER', 'STAFF') NOT NULL DEFAULT 'STAFF'");
    }
};
