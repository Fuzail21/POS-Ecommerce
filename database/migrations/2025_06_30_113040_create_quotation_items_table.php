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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Assuming 'products' table exists
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade'); // Assuming 'product_variants' table exists

            $table->decimal('unit_price', 10, 2); // Price at the time of quotation
            $table->integer('quantity');
            $table->decimal('discount_amount', 10, 2)->default(0.00); // Discount applied to this item
            $table->decimal('tax_amount', 10, 2)->default(0.00); // Tax applied to this item
            $table->decimal('subtotal', 10, 2); // (unit_price * quantity) - discount_amount + tax_amount
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
