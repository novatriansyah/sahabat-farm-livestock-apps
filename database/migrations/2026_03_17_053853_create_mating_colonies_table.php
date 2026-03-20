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
        Schema::create('mating_colonies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('partner_id')->nullable()->constrained('master_partners')->nullOnDelete();
            $table->string('name');
            $table->foreignUuid('sire_id')->constrained('animals')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('master_locations');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['ACTIVE', 'COMPLETED'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mating_colonies');
    }
};
