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
        Schema::table('treatment_logs', function (Blueprint $table) {
            $table->foreignId('disease_id')->nullable()->constrained('master_diseases')->nullOnDelete()->after('type');
        });

        Schema::table('exit_logs', function (Blueprint $table) {
            $table->foreignId('disease_id')->nullable()->constrained('master_diseases')->nullOnDelete()->after('exit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatment_logs', function (Blueprint $table) {
            $table->dropForeign(['disease_id']);
            $table->dropColumn('disease_id');
        });

        Schema::table('exit_logs', function (Blueprint $table) {
            $table->dropForeign(['disease_id']);
            $table->dropColumn('disease_id');
        });
    }
};
