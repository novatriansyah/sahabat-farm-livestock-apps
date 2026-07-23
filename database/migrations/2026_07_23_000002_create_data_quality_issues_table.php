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
        if (!Schema::hasTable('data_quality_issues')) {
            Schema::create('data_quality_issues', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('idempotency_key')->unique();
                $table->string('issue_code')->nullable();           // e.g. 'MISSING_BIRTH_WEIGHT'
                $table->string('record_type')->default('ANIMAL');
                $table->string('record_id')->nullable();             // Primary lookup key (UUID)
                $table->uuid('animal_id')->nullable();              // Alias for record_id (backward compat)
                $table->string('tag_id')->nullable();
                $table->string('category')->default('GENERAL');
                $table->string('field_name')->nullable();
                $table->text('description')->nullable();
                $table->string('severity')->default('CONDITIONALLY_REQUIRED');
                $table->string('status')->default('OPEN');
                $table->json('blocked_processes')->nullable();
                $table->string('assigned_role')->nullable();
                $table->string('remediation_url')->nullable();
                $table->text('evidence_needed')->nullable();
                $table->string('resolved_by')->nullable();          // UUID of resolving user
                $table->timestamp('resolved_at')->nullable();
                $table->json('audit_trail')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_quality_issues');
    }
};
