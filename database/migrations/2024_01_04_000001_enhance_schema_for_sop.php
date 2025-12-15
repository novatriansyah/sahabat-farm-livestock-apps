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
        // 1. Master Diseases
        Schema::create('master_diseases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('symptoms')->nullable();
            // Optional: Link to a recommended medicine if needed
            $table->foreignId('recommended_treatment_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Update Master Breeds
        Schema::table('master_breeds', function (Blueprint $table) {
            $table->string('origin')->nullable();
            $table->float('min_weight_mate')->nullable(); // kg
            $table->integer('min_age_mate_months')->nullable();
        });

        // 3. Update Inventory Items
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('category')->nullable(); // MEDICINE, FEED, etc.
            $table->float('dosage_per_kg')->nullable(); // for Medicines
        });

        // 4. Breeding Events
        Schema::create('breeding_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dam_id')->constrained('animals')->cascadeOnDelete();
            $table->foreignUuid('sire_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->date('mating_date');
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING'); // SUCCESS=Pregnant
            $table->timestamps();
        });

        // 5. Animal Tasks (SOP)
        Schema::create('animal_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('animal_id')->constrained('animals')->cascadeOnDelete();
            $table->string('title');
            $table->enum('status', ['PENDING', 'COMPLETED'])->default('PENDING');
            $table->date('due_date')->nullable();
            $table->string('type')->default('GENERAL'); // ARRIVAL, HEALTH, ETC
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_tasks');
        Schema::dropIfExists('breeding_events');

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['category', 'dosage_per_kg']);
        });

        Schema::table('master_breeds', function (Blueprint $table) {
            $table->dropColumn(['origin', 'min_weight_mate', 'min_age_mate_months']);
        });

        Schema::dropIfExists('master_diseases');
    }
};
