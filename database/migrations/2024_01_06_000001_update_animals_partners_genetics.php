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
        Schema::table('animals', function (Blueprint $table) {
            // Partner Ownership
            $table->foreignId('partner_id')->nullable()->after('tag_id')->constrained('master_partners')->nullOnDelete();

            // Visual Identification
            $table->string('necklace_color')->nullable()->after('gender');
            $table->string('ear_tag_color')->nullable()->after('necklace_color');
            $table->string('generation')->nullable()->after('ear_tag_color'); // F1, F2, F3, etc.

            // Allow owner_id to be null logic:
            // 1. Drop FK (Pass column name array to generate 'animals_owner_id_foreign')
            $table->dropForeign(['owner_id']);

            // 2. Modify Column to be nullable UUID
            // Note: 'users.id' is UUID, so this must be UUID/CHAR(36)
            $table->uuid('owner_id')->nullable()->change();

            // 3. Re-add FK
            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['partner_id', 'necklace_color', 'ear_tag_color', 'generation']);

            // Revert owner_id
            $table->dropForeign(['owner_id']);
            $table->uuid('owner_id')->nullable(false)->change();
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
