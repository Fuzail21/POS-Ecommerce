<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Remove unit from categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('unit');
        });

        // Add unit to products
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('unit')->nullable()->after('selling_price');
            // If you want to enforce foreign key constraint:
            // $table->foreign('unit')->references('id')->on('units')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Revert changes
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('unit');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('unit')->nullable()->after('id');
        });
    }
};

