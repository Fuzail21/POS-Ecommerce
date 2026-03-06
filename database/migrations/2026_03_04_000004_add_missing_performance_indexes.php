<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users — role_id and branch_id frequently used in RBAC and branch-scoping queries
        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id',   'idx_users_role_id');
            $table->index('branch_id', 'idx_users_branch_id');
        });

        // products — category_id used in shop filter and discount rules
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id',   'idx_products_category_id');
            $table->index('base_unit_id',  'idx_products_base_unit_id');
        });

        // sales — multi-column filter on origin + status + branch for lists and reports
        Schema::table('sales', function (Blueprint $table) {
            $table->index('branch_id',   'idx_sales_branch_id');
            $table->index('customer_id', 'idx_sales_customer_id');
            $table->index(['sale_origin', 'status'], 'idx_sales_origin_status');
        });

        // purchases — supplier and warehouse used in purchase list and reports
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('supplier_id',  'idx_purchases_supplier_id');
            $table->index('warehouse_id', 'idx_purchases_warehouse_id');
            $table->index('created_by',   'idx_purchases_created_by');
        });

        // stock_ledgers — ref_type + ref_id queried when loading ledger for a sale/purchase
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->index(['ref_type', 'ref_id'], 'idx_stock_ledgers_ref');
        });

        // customers — phone used for SMS lookup
        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone', 'idx_customers_phone');
        });

        // payment_transactions — status + gateway used in payment reporting
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->index('status',  'idx_payment_transactions_status');
            $table->index('gateway', 'idx_payment_transactions_gateway');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_id');
            $table->dropIndex('idx_users_branch_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_base_unit_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_branch_id');
            $table->dropIndex('idx_sales_customer_id');
            $table->dropIndex('idx_sales_origin_status');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('idx_purchases_supplier_id');
            $table->dropIndex('idx_purchases_warehouse_id');
            $table->dropIndex('idx_purchases_created_by');
        });

        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropIndex('idx_stock_ledgers_ref');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_phone');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_payment_transactions_status');
            $table->dropIndex('idx_payment_transactions_gateway');
        });
    }
};
