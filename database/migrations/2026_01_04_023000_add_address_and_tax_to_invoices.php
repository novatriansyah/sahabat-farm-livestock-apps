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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('customer_address')->nullable()->after('customer_contact');
            $table->decimal('tax_rate', 5, 2)->nullable()->comment('PPN Percentage (e.g. 11.00)');
            $table->decimal('additional_tax_rate', 5, 2)->nullable()->comment('Future Tax Percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['customer_address', 'tax_rate', 'additional_tax_rate']);
        });
    }
};
