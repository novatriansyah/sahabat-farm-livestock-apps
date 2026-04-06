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
        Schema::table('exit_logs', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('price');
            $table->string('customer_contact')->nullable()->after('customer_name');
            $table->text('notes')->nullable()->after('customer_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exit_logs', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_contact', 'notes']);
        });
    }
};
