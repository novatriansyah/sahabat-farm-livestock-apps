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
        Schema::table('animals', function (Blueprint $table) {
            $table->decimal('accumulated_feed_cost', 15, 2)->default(0)->after('purchase_price');
            $table->decimal('accumulated_medicine_cost', 15, 2)->default(0)->after('accumulated_feed_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn(['accumulated_feed_cost', 'accumulated_medicine_cost']);
        });
    }
};
