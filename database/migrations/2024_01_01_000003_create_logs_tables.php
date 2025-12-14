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
        Schema::create('weight_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->date('weigh_date');
            $table->float('weight_kg');
            $table->timestamps();
        });

        Schema::create('treatment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->date('treatment_date');
            $table->string('type'); // Vaccine/Vitamin
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('exit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->date('exit_date');
            $table->enum('exit_type', ['DEATH', 'SALE']);
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('final_hpp', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('animal_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->string('photo_url');
            $table->date('capture_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_photos');
        Schema::dropIfExists('exit_logs');
        Schema::dropIfExists('treatment_logs');
        Schema::dropIfExists('weight_logs');
    }
};
