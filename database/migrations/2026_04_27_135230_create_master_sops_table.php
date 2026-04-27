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
        Schema::create('master_sops', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // ARRIVAL, BIRTH, ROUTINE
            $table->string('title');
            $table->string('task_type'); // HEALTH, ARRIVAL, ROUTINE, GENERAL
            $table->integer('due_days_offset')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sops');
    }
};
