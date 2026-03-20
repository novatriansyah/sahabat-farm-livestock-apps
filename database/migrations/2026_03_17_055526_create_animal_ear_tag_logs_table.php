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
        Schema::create('animal_ear_tag_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('animal_id')->constrained('animals')->onDelete('cascade');
            $table->string('old_tag_id');
            $table->string('new_tag_id');
            $table->date('changed_at');
            $table->text('reason')->nullable();
            $table->foreignUuid('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_ear_tag_logs');
    }
};
