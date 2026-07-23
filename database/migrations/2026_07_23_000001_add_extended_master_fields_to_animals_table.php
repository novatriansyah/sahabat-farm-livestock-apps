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
            if (!Schema::hasColumn('animals', 'legacy_tag_id')) {
                $table->string('legacy_tag_id')->nullable();
            }
            if (!Schema::hasColumn('animals', 'declared_generation')) {
                $table->string('declared_generation')->nullable();
            }
            if (!Schema::hasColumn('animals', 'physical_characteristics')) {
                $table->text('physical_characteristics')->nullable();
            }
            if (!Schema::hasColumn('animals', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('animals', 'litter_size')) {
                $table->string('litter_size')->nullable();
            }
            if (!Schema::hasColumn('animals', 'birth_weight')) {
                $table->decimal('birth_weight', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('animals', 'valuation')) {
                $table->decimal('valuation', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('animals', 'data_source')) {
                $table->string('data_source')->nullable();
            }
            if (!Schema::hasColumn('animals', 'confidence')) {
                $table->string('confidence')->nullable();
            }
            if (!Schema::hasColumn('animals', 'in_partner_file')) {
                $table->boolean('in_partner_file')->default(false);
            }
            if (!Schema::hasColumn('animals', 'birth_event_ref')) {
                $table->string('birth_event_ref')->nullable();
            }
            if (!Schema::hasColumn('animals', 'current_inventory_status')) {
                $table->string('current_inventory_status')->default('TERSEDIA');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $cols = [
                'legacy_tag_id',
                'declared_generation',
                'physical_characteristics',
                'notes',
                'litter_size',
                'birth_weight',
                'valuation',
                'data_source',
                'confidence',
                'in_partner_file',
                'birth_event_ref',
                'current_inventory_status',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('animals', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
