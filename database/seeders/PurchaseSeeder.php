<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        // Purchase 1 - Electronics from Al-Harmain Traders
        DB::table('purchases')->insert([
            'supplier_id'    => 1,
            'warehouse_id'   => 1,
            'invoice_number' => 'PUR-2026-0001',
            'purchase_date'  => '2026-02-01',
            'total_amount'   => 295000.00,
            'paid_amount'    => 295000.00,
            'due_amount'     => 0.00,
            'notes'          => 'First electronics order - TVs, fridges, and washing machines',
            'created_by'     => 1,
            'created_at'     => now(), 'updated_at' => now(),
        ]);
        DB::table('purchase_items')->insert([
            ['purchase_id' => 1, 'product_id' => 1, 'variant_id' => null, 'quantity' => 2, 'unit_id' => 30, 'quantity_in_base_unit' => 2, 'unit_cost' => 72000.00, 'total_cost' => 144000.00, 'batch_no' => 'BATCH-001', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 1, 'product_id' => 2, 'variant_id' => null, 'quantity' => 2, 'unit_id' => 30, 'quantity_in_base_unit' => 2, 'unit_cost' => 55000.00, 'total_cost' => 110000.00, 'batch_no' => 'BATCH-002', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 1, 'product_id' => 3, 'variant_id' => null, 'quantity' => 2, 'unit_id' => 30, 'quantity_in_base_unit' => 2, 'unit_cost' => 20500.00, 'total_cost' => 41000.00,  'batch_no' => 'BATCH-003', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Purchase 2 - Grocery from Punjab Foods & Grains
        DB::table('purchases')->insert([
            'supplier_id'    => 3,
            'warehouse_id'   => 1,
            'invoice_number' => 'PUR-2026-0002',
            'purchase_date'  => '2026-02-03',
            'total_amount'   => 320000.00,
            'paid_amount'    => 200000.00,
            'due_amount'     => 120000.00,
            'notes'          => 'Monthly grocery stock - flour, rice, oil',
            'created_by'     => 1,
            'created_at'     => now(), 'updated_at' => now(),
        ]);
        DB::table('purchase_items')->insert([
            ['purchase_id' => 2, 'product_id' => 6, 'variant_id' => null, 'quantity' => 100, 'unit_id' => 32, 'quantity_in_base_unit' => 1000, 'unit_cost' => 1200.00, 'total_cost' => 120000.00, 'batch_no' => 'GRO-001', 'expiry_date' => '2026-12-31', 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 2, 'product_id' => 7, 'variant_id' => null, 'quantity' => 80,  'unit_id' => 32, 'quantity_in_base_unit' => 400,  'unit_cost' => 950.00,  'total_cost' => 76000.00,  'batch_no' => 'GRO-002', 'expiry_date' => '2026-10-31', 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 2, 'product_id' => 8, 'variant_id' => null, 'quantity' => 60,  'unit_id' => 36, 'quantity_in_base_unit' => 300,  'unit_cost' => 2100.00, 'total_cost' => 126000.00, 'batch_no' => 'GRO-003', 'expiry_date' => '2027-06-30', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Purchase 3 - Mobiles from HiTech Mobile Distributors
        DB::table('purchases')->insert([
            'supplier_id'    => 5,
            'warehouse_id'   => 1,
            'invoice_number' => 'PUR-2026-0003',
            'purchase_date'  => '2026-02-07',
            'total_amount'   => 1670000.00,
            'paid_amount'    => 1670000.00,
            'due_amount'     => 0.00,
            'notes'          => 'Mobile phone stock - Samsung A54 and Tecno Spark 20',
            'created_by'     => 1,
            'created_at'     => now(), 'updated_at' => now(),
        ]);
        DB::table('purchase_items')->insert([
            ['purchase_id' => 3, 'product_id' => 4, 'variant_id' => 1, 'quantity' => 10, 'unit_id' => 30, 'quantity_in_base_unit' => 10, 'unit_cost' => 68000.00, 'total_cost' => 680000.00, 'batch_no' => 'MOB-001', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 3, 'product_id' => 4, 'variant_id' => 2, 'quantity' => 8,  'unit_id' => 30, 'quantity_in_base_unit' => 8,  'unit_cost' => 80000.00, 'total_cost' => 640000.00, 'batch_no' => 'MOB-002', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 3, 'product_id' => 5, 'variant_id' => 4, 'quantity' => 15, 'unit_id' => 30, 'quantity_in_base_unit' => 15, 'unit_cost' => 23000.00, 'total_cost' => 345000.00, 'batch_no' => 'MOB-003', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
            ['purchase_id' => 3, 'product_id' => 5, 'variant_id' => 5, 'quantity' => 5,  'unit_id' => 30, 'quantity_in_base_unit' => 5,  'unit_cost' => 1000.00,  'total_cost' => 5000.00,   'batch_no' => 'MOB-004', 'expiry_date' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
