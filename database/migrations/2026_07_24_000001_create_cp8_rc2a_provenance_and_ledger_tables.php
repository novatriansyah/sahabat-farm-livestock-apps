<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for CP8 RC2-A Provenance, Field Changes Audit, HPP Ledgers, and Recalculation Runs.
     */
    public function up(): void
    {
        // Make daily_adg nullable on animals table so NOT_CALCULABLE returns NULL instead of 0
        Schema::table('animals', function (Blueprint $table) {
            $table->float('daily_adg')->nullable()->change();
        });

        // 1. Additive columns for weight_logs
        Schema::table('weight_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('weight_logs', 'measurement_status')) {
                $table->string('measurement_status')->default('ACTUAL'); // ACTUAL, ESTIMATED, ASSUMED
            }
            if (!Schema::hasColumn('weight_logs', 'source')) {
                $table->string('source')->nullable();
            }
            if (!Schema::hasColumn('weight_logs', 'confidence')) {
                $table->string('confidence')->nullable();
            }
            if (!Schema::hasColumn('weight_logs', 'recorded_by')) {
                $table->string('recorded_by')->nullable();
            }
            if (!Schema::hasColumn('weight_logs', 'supersedes_id')) {
                $table->unsignedBigInteger('supersedes_id')->nullable();
            }
            if (!Schema::hasColumn('weight_logs', 'is_current')) {
                $table->boolean('is_current')->default(true);
            }
            if (!Schema::hasColumn('weight_logs', 'correction_reason')) {
                $table->text('correction_reason')->nullable();
            }
        });

        // 2. Audit Trail for master attribute changes
        if (!Schema::hasTable('animal_field_changes')) {
            Schema::create('animal_field_changes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
                $table->string('field_name');
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->string('old_value_status')->nullable(); // ACTUAL, ESTIMATED, ASSUMED, UNKNOWN
                $table->string('new_value_status')->nullable(); // ACTUAL, ESTIMATED, ASSUMED, UNKNOWN
                $table->string('source')->nullable();
                $table->text('reason')->nullable();
                $table->string('changed_by')->nullable();
                $table->timestamp('changed_at')->useCurrent();
                $table->string('correlation_id')->nullable();
                $table->timestamps();
            });
        }

        // 3. Replayable & Idempotent HPP Allocation Ledger
        if (!Schema::hasTable('hpp_allocations')) {
            Schema::create('hpp_allocations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
                $table->string('source_type'); // FEED_USAGE, TREATMENT_LOG, MANUAL_COST, INITIAL_ACQUISITION
                $table->string('source_id');
                $table->date('effective_date');
                $table->unsignedBigInteger('partner_id')->nullable();
                $table->unsignedBigInteger('location_id')->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('allocation_rule_version')->default('1.0');
                $table->string('idempotency_key')->unique();
                $table->string('status')->default('ACTIVE'); // ACTIVE, REVERSED
                $table->uuid('reversal_ref_id')->nullable();
                $table->timestamps();
            });
        }

        // 4. Derived Calculation Run Tracker
        if (!Schema::hasTable('derived_calculation_runs')) {
            Schema::create('derived_calculation_runs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('trigger_correlation')->nullable();
                $table->string('affected_entity_type'); // ANIMAL, PARTNER, SYSTEM
                $table->string('affected_entity_id')->nullable();
                $table->string('affected_date_range')->nullable();
                $table->string('formula_version')->default('1.0');
                $table->string('status')->default('STARTED'); // STARTED, COMPLETED, FAILED
                $table->text('error_message')->nullable();
                $table->string('result_checksum')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('derived_calculation_runs');
        Schema::dropIfExists('hpp_allocations');
        Schema::dropIfExists('animal_field_changes');

        Schema::table('weight_logs', function (Blueprint $table) {
            $table->dropColumn([
                'measurement_status',
                'source',
                'confidence',
                'recorded_by',
                'supersedes_id',
                'is_current',
                'correction_reason',
            ]);
        });
    }
};
