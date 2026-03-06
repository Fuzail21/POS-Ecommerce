<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockLedgerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ─── PURCHASE 1 (id=1) — Electronics into WH#1 ───────────────────────
        DB::table('stock_ledgers')->insert([
            // product 1: Samsung LED TV (qty=2)
            ['product_id' => 1, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 1, 'quantity_change_in_base_unit' => 2, 'unit_cost' => 72000.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 2: Haier Refrigerator (qty=2)
            ['product_id' => 2, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 1, 'quantity_change_in_base_unit' => 2, 'unit_cost' => 55000.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 3: Dawlance Washing Machine (qty=2)
            ['product_id' => 3, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 1, 'quantity_change_in_base_unit' => 2, 'unit_cost' => 20500.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── PURCHASE 2 (id=2) — Grocery into WH#1 ───────────────────────────
        DB::table('stock_ledgers')->insert([
            // product 6: Wheat Flour (qty=1000 kg)
            ['product_id' => 6, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 2, 'quantity_change_in_base_unit' => 1000, 'unit_cost' => 1200.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 7: Basmati Rice (qty=400 kg)
            ['product_id' => 7, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 2, 'quantity_change_in_base_unit' => 400,  'unit_cost' =>  950.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 8: Cooking Oil (qty=300 L)
            ['product_id' => 8, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 2, 'quantity_change_in_base_unit' => 300,  'unit_cost' => 2100.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── PURCHASE 3 (id=3) — Mobiles into WH#1 ───────────────────────────
        DB::table('stock_ledgers')->insert([
            // product 4, variant 1: Samsung A54 6GB/128GB Black (qty=10)
            ['product_id' => 4, 'variant_id' => 1, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 3, 'quantity_change_in_base_unit' => 10, 'unit_cost' => 68000.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 4, variant 2: Samsung A54 8GB/256GB Blue (qty=8)
            ['product_id' => 4, 'variant_id' => 2, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 3, 'quantity_change_in_base_unit' =>  8, 'unit_cost' => 80000.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 5, variant 4: Tecno Spark 20 8GB/128GB Black (qty=15)
            ['product_id' => 5, 'variant_id' => 4, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 3, 'quantity_change_in_base_unit' => 15, 'unit_cost' => 23000.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // product 5, variant 5: Tecno Spark 20 8GB/128GB Gold (qty=5)
            ['product_id' => 5, 'variant_id' => 5, 'warehouse_id' => 1, 'ref_type' => 'purchase', 'ref_id' => 3, 'quantity_change_in_base_unit' =>  5, 'unit_cost' =>  1000.00, 'direction' => 'in', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── STOCK TRANSFER 1 (id=1) — WH#1 → WH#5 (Lahore Main Branch Store) ──
        DB::table('stock_ledgers')->insert([
            // OUT from WH#1 — product 6 Wheat Flour (qty=200 packs)
            ['product_id' => 6,  'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 200, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IN to WH#5 — product 6 Wheat Flour (qty=200 packs)
            ['product_id' => 6,  'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 200, 'unit_cost' => null, 'direction' => 'in',  'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // OUT from WH#1 — product 7 Basmati Rice (qty=100 packs)
            ['product_id' => 7,  'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 100, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IN to WH#5 — product 7 Basmati Rice (qty=100 packs)
            ['product_id' => 7,  'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 100, 'unit_cost' => null, 'direction' => 'in',  'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // OUT from WH#1 — product 4 variant 1 Samsung A54 Black (qty=5)
            ['product_id' => 4,  'variant_id' => 1,    'warehouse_id' => 1, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 5,   'unit_cost' => null, 'direction' => 'out', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IN to WH#5 — product 4 variant 1 Samsung A54 Black (qty=5)
            ['product_id' => 4,  'variant_id' => 1,    'warehouse_id' => 5, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 5,   'unit_cost' => null, 'direction' => 'in',  'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // OUT from WH#1 — product 12 Pond's Cream (qty=40)
            ['product_id' => 12, 'variant_id' => null, 'warehouse_id' => 1, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 40,  'unit_cost' => null, 'direction' => 'out', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IN to WH#5 — product 12 Pond's Cream (qty=40)
            ['product_id' => 12, 'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'transfer', 'ref_id' => 1, 'quantity_change_in_base_unit' => 40,  'unit_cost' => null, 'direction' => 'in',  'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── STOCK TRANSFER 2 (id=2) — WH#2 → WH#6 (Karachi Center Branch Store) ──
        DB::table('stock_ledgers')->insert([
            // OUT from WH#2 — product 6 Wheat Flour (qty=150)
            ['product_id' => 6, 'variant_id' => null, 'warehouse_id' => 2, 'ref_type' => 'transfer', 'ref_id' => 2, 'quantity_change_in_base_unit' => 150, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IN to WH#6 — product 6 Wheat Flour (qty=150)
            ['product_id' => 6, 'variant_id' => null, 'warehouse_id' => 6, 'ref_type' => 'transfer', 'ref_id' => 2, 'quantity_change_in_base_unit' => 150, 'unit_cost' => null, 'direction' => 'in',  'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // OUT from WH#2 — product 7 Basmati Rice (qty=80)
            ['product_id' => 7, 'variant_id' => null, 'warehouse_id' => 2, 'ref_type' => 'transfer', 'ref_id' => 2, 'quantity_change_in_base_unit' =>  80, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
            // IN to WH#6 — product 7 Basmati Rice (qty=80)
            ['product_id' => 7, 'variant_id' => null, 'warehouse_id' => 6, 'ref_type' => 'transfer', 'ref_id' => 2, 'quantity_change_in_base_unit' =>  80, 'unit_cost' => null, 'direction' => 'in',  'created_by' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── SALE 1 (id=1) — POS at Branch 1 (WH#5) ─────────────────────────
        DB::table('stock_ledgers')->insert([
            // product 6: Wheat Flour (qty=2)
            ['product_id' => 6,  'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 1, 'quantity_change_in_base_unit' => 2, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
            // product 9: Chana Dal (qty=2)
            ['product_id' => 9,  'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 1, 'quantity_change_in_base_unit' => 2, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
            // product 12: Pond's Cream (qty=1)
            ['product_id' => 12, 'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 1, 'quantity_change_in_base_unit' => 1, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── SALE 2 (id=2) — Mobile sale at Branch 1 (WH#5) ────────────────
        DB::table('stock_ledgers')->insert([
            // product 4 variant 1: Samsung A54 Black (qty=1)
            ['product_id' => 4, 'variant_id' => 1, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 2, 'quantity_change_in_base_unit' => 1, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── SALE 3 (id=3) — E-commerce at Branch 2 (WH#6) ────────────────
        DB::table('stock_ledgers')->insert([
            // product 11 variant 11: Women Lawn Suit S-Pink (qty=1)
            ['product_id' => 11, 'variant_id' => 11, 'warehouse_id' => 6, 'ref_type' => 'sale', 'ref_id' => 3, 'quantity_change_in_base_unit' => 1, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 6, 'created_at' => $now, 'updated_at' => $now],
            // product 11 variant 12: Women Lawn Suit M-Pink (qty=1)
            ['product_id' => 11, 'variant_id' => 12, 'warehouse_id' => 6, 'ref_type' => 'sale', 'ref_id' => 3, 'quantity_change_in_base_unit' => 1, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── SALE 4 (id=4) — Grocery bulk at Branch 1 (WH#5) ───────────────
        DB::table('stock_ledgers')->insert([
            // product 6: Wheat Flour (qty=5)
            ['product_id' => 6, 'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 4, 'quantity_change_in_base_unit' => 5, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
            // product 7: Basmati Rice (qty=3)
            ['product_id' => 7, 'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 4, 'quantity_change_in_base_unit' => 3, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
            // product 8: Cooking Oil (qty=1)
            ['product_id' => 8, 'variant_id' => null, 'warehouse_id' => 5, 'ref_type' => 'sale', 'ref_id' => 4, 'quantity_change_in_base_unit' => 1, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ─── SALE 5 (id=5) — E-commerce pending at Branch 3 (WH#7) ────────
        DB::table('stock_ledgers')->insert([
            // product 5 variant 4: Tecno Spark 20 Black (qty=1)
            ['product_id' => 5, 'variant_id' => 4, 'warehouse_id' => 7, 'ref_type' => 'sale', 'ref_id' => 5, 'quantity_change_in_base_unit' => 1, 'unit_cost' => null, 'direction' => 'out', 'created_by' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
