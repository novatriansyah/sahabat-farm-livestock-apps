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
        Schema::create('mating_colony_members', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('mating_colony_id')->constrained('mating_colonies')->cascadeOnDelete();
            $table->foreignUuid('dam_id')->constrained('animals')->cascadeOnDelete();
            $table->date('joined_date');
            $table->date('left_date')->nullable();
            $table->enum('status', ['MATING', 'PREGNANT', 'FAILED', 'NURSING', 'READY'])->default('MATING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mating_colony_members');
    }
};
