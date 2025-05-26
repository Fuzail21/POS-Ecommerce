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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('entity_type', ['customer', 'supplier']);
            $table->unsignedBigInteger('entity_id');
            $table->enum('transaction_type', ['in', 'out']);
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'card', 'bank']);
            $table->enum('ref_type', ['sale', 'purchase', 'manual']);
            $table->unsignedBigInteger('ref_id');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
            $table->timestamps();

            // Optional: add indexes or foreign keys if needed
            $table->index(['entity_type', 'entity_id']);
            $table->index(['ref_type', 'ref_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
