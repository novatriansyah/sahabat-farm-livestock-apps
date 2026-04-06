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
        Schema::table('master_diseases', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name'); // Viral, Bakteri, Parasit, Pakan, dll.
            $table->text('description')->nullable()->after('symptoms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_diseases', function (Blueprint $table) {
            $table->dropColumn(['category', 'description']);
        });
    }
};
