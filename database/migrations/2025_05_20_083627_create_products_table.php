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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product_img')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('base_unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('default_display_unit_id')->constrained('units')->onDelete('cascade');
            $table->boolean('has_variants')->default(false);
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('brand')->nullable();
            $table->boolean('track_expiry')->default(false);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
