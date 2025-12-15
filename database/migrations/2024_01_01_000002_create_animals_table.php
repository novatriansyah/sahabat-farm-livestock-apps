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
        Schema::create('animals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tag_id')->unique();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();

            // Self-referencing FKs (Bapak/Induk)
            $table->foreignUuid('sire_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->foreignUuid('dam_id')->nullable()->constrained('animals')->nullOnDelete();

            $table->foreignId('category_id')->constrained('master_categories');
            $table->foreignId('breed_id')->constrained('master_breeds');
            $table->foreignId('current_location_id')->constrained('master_locations');
            $table->foreignId('current_phys_status_id')->constrained('master_phys_statuses');

            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->date('birth_date');
            $table->enum('acquisition_type', ['BRED', 'BOUGHT']);

            $table->boolean('is_active')->default(true)->index();
            $table->enum('health_status', ['HEALTHY', 'SICK', 'QUARANTINE', 'DECEASED', 'SOLD'])->default('HEALTHY');

            // Financial Metrics
            $table->decimal('current_hpp', 15, 2)->default(0);

            // Growth Metrics
            $table->float('daily_adg')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
