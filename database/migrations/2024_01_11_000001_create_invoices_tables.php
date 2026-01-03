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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique(); // e.g., INV/2024/001
            $table->string('customer_name');
            $table->string('customer_contact')->nullable();
            
            // Workflow: DRAFT -> ISSUED -> PAID | CANCELLED
            // For Proforma: DRAFT (implied) -> ISSUED. If accepted -> Convert to COMMERCIAL (New Record or same? Better different or type switch)
            // Implementation Plan: One table, type column toggles.
            $table->enum('status', ['DRAFT', 'ISSUED', 'PAID', 'CANCELLED'])->default('DRAFT');
            $table->enum('type', ['PROFORMA', 'COMMERCIAL'])->default('PROFORMA');
            
            $table->date('issued_date');
            $table->date('due_date')->nullable();
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0); // If needed later
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            
            // Optional: Link to specific animal if this item is a sale of an animal
            $table->foreignUuid('related_animal_id')->nullable()->constrained('animals')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
