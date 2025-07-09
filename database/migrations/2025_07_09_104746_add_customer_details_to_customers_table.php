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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('name');
            // Add country, city, postcode (assuming these are strings)
            $table->string('country')->nullable()->after('address');
            $table->string('city')->nullable()->after('country');
            $table->string('postcode')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'country', 'city', 'postcode']);
        });
    }
};
