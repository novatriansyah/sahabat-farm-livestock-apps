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
        // Add entry_date to animals table to track when they arrived/entered the system physically.
        // For existing records, we default to created_at.
        Schema::table('animals', function (Blueprint $table) {
            $table->date('entry_date')->nullable()->after('birth_date');
        });

        // Update existing records
        DB::statement('UPDATE animals SET entry_date = DATE(created_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn('entry_date');
        });
    }
};
