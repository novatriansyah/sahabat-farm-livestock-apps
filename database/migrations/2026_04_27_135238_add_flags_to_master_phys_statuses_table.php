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
        Schema::table('master_phys_statuses', function (Blueprint $table) {
            $table->boolean('is_breedable')->default(true)->after('name');
            $table->boolean('is_quarantine')->default(false)->after('is_breedable');
            $table->boolean('is_lactating')->default(false)->after('is_quarantine');
            $table->boolean('is_pregnant')->default(false)->after('is_lactating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_phys_statuses', function (Blueprint $table) {
            $table->dropColumn(['is_breedable', 'is_quarantine', 'is_lactating', 'is_pregnant']);
        });
    }
};
