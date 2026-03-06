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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('gateway');                         // 'jazzcash' | 'easypaisa'
            $table->string('transaction_id')->nullable();      // gateway reference number
            $table->string('pp_response_code')->nullable();    // gateway response code
            $table->string('pp_response_message')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');      // pending | success | failed
            $table->json('gateway_payload')->nullable();       // raw callback payload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
