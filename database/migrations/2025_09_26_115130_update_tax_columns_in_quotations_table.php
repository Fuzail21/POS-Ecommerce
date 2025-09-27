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
        Schema::table('quotations', function (Blueprint $table) {
            // Rename column
            $table->renameColumn('order_tax_percentage', 'order_tax_amount');

            // Add new column
            $table->decimal('tax_percentage', 5, 2)->after('order_tax_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Rollback changes
            $table->renameColumn('order_tax_amount', 'order_tax_percentage');
            $table->dropColumn('tax_percentage');
        });
    }
};
