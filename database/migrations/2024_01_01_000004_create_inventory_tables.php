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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit'); // kg/sak
            $table->float('current_stock')->default(0);
            $table->timestamps();
        });

        Schema::create('inventory_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->date('date');
            $table->float('qty');
            $table->decimal('price_total', 15, 2);
            $table->timestamps();
        });

        Schema::create('inventory_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->date('usage_date');
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->float('qty_used');
            $table->float('qty_wasted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_usage_logs');
        Schema::dropIfExists('inventory_purchases');
        Schema::dropIfExists('inventory_items');
    }
};
