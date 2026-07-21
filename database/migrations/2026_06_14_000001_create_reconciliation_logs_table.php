<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliation_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('batch_id')->index();
            $table->enum('status', ['SAME', 'WEB_ONLY', 'EXCEL_ONLY', 'CONFLICT', 'UNCERTAIN']);
            $table->uuid('animal_id')->nullable()->index();
            $table->string('tag_id', 50)->nullable();
            $table->string('field', 80)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->float('confidence', 8, 4)->default(1.0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_logs');
    }
};