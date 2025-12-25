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
        // Modify the enum to include 'COMPLETED'
        // Using raw statement for Enum modification as it's not standard in Blueprint
        DB::statement("ALTER TABLE breeding_events MODIFY COLUMN status ENUM('PENDING', 'SUCCESS', 'FAILED', 'COMPLETED') NOT NULL DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert (Warning: 'COMPLETED' data might be truncated or cause error if not cleaned up)
        // We will map COMPLETED back to PENDING if rolling back
        DB::table('breeding_events')->where('status', 'COMPLETED')->update(['status' => 'PENDING']);

        DB::statement("ALTER TABLE breeding_events MODIFY COLUMN status ENUM('PENDING', 'SUCCESS', 'FAILED') NOT NULL DEFAULT 'PENDING'");
    }
};
