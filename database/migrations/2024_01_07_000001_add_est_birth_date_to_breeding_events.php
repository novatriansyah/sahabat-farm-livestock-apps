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
        Schema::table('breeding_events', function (Blueprint $table) {
            $table->date('est_birth_date')->nullable()->after('mating_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breeding_events', function (Blueprint $table) {
            $table->dropColumn('est_birth_date');
        });
    }
};
