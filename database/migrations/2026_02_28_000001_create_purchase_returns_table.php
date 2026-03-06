<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->date('return_date');
            $table->text('return_reason')->nullable();
            $table->decimal('total_return_amount', 15, 2)->default(0);
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
