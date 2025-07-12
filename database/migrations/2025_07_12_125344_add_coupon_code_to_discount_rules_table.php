<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade for raw SQL


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('discount_rules', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->after('type');
        });

        DB::statement("ALTER TABLE discount_rules MODIFY COLUMN type ENUM('category', 'product', 'coupon') NOT NULL;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_rules', function (Blueprint $table) {
            $table->dropColumn('coupon_code');
        });
        DB::statement("ALTER TABLE discount_rules MODIFY COLUMN type ENUM('category', 'product') NOT NULL;");
    }
};
