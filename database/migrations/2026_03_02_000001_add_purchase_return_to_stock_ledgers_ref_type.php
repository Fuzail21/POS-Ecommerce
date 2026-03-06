<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE stock_ledgers MODIFY ref_type ENUM('purchase','sale','return','adjustment','transfer','cancelled_order_return','purchase_return') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stock_ledgers MODIFY ref_type ENUM('purchase','sale','return','adjustment','transfer','cancelled_order_return') NOT NULL");
    }
};
