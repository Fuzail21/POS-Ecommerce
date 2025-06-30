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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // Assuming 'customers' table exists
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null'); // Assuming 'warehouses' table exists
            $table->date('quotation_date');
            $table->decimal('order_tax_percentage', 10, 2)->default(0.00); // e.g., 5.00 for 5%
            $table->decimal('discount_percentage', 10, 2)->default(0.00); // e.g., 10.00 for 10%
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('grand_total', 10, 2)->default(0.00);
            $table->string('status')->default('Pending'); // e.g., Pending, Sent, Accepted, Rejected
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
