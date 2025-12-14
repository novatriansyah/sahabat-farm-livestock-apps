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
        Schema::create('master_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('master_breeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('master_categories')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('master_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // e.g., Kandang Individu, Koloni
            $table->timestamps();
        });

        Schema::create('master_phys_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('rules')->nullable(); // e.g., Cempe, Siap Kawin
            $table->timestamps();
        });

        Schema::create('master_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('contact_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_partners');
        Schema::dropIfExists('master_phys_statuses');
        Schema::dropIfExists('master_locations');
        Schema::dropIfExists('master_breeds');
        Schema::dropIfExists('master_categories');
    }
};
