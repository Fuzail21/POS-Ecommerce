<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryStockSeeder extends Seeder
{
    public function run(): void
    {
        // Central purchasing warehouses (1=Lahore, 2=Karachi, 3=Islamabad)
        // Branch store warehouses    (5=Lahore Main, 6=Karachi Center, 7=Islamabad, 8=Peshawar, 9=Lahore Gulberg)
        //
        // Products without variants: 1,2,3,6,7,8,9,12,13
        // Products with variants: 4(var 1-3), 5(var 4-5), 10(var 6-10), 11(var 11-14)

        $stocks = [
            // ── Central WH#1 (Lahore) — purchasing stock ─────────────────────
            ['product_id' => 1,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 10],
            ['product_id' => 2,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 8],
            ['product_id' => 3,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 12],
            ['product_id' => 6,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 600],
            ['product_id' => 7,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 300],
            ['product_id' => 8,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 250],
            ['product_id' => 9,  'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 500],
            ['product_id' => 12, 'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 80],
            ['product_id' => 13, 'variant_id' => null, 'warehouse_id' => 1, 'quantity_in_base_unit' => 60],
            // Samsung Galaxy A54 variants in WH#1
            ['product_id' => 4, 'variant_id' => 1, 'warehouse_id' => 1, 'quantity_in_base_unit' => 10],
            ['product_id' => 4, 'variant_id' => 2, 'warehouse_id' => 1, 'quantity_in_base_unit' => 8],
            ['product_id' => 4, 'variant_id' => 3, 'warehouse_id' => 1, 'quantity_in_base_unit' => 8],
            // Tecno Spark 20 variants in WH#1
            ['product_id' => 5, 'variant_id' => 4, 'warehouse_id' => 1, 'quantity_in_base_unit' => 10],
            ['product_id' => 5, 'variant_id' => 5, 'warehouse_id' => 1, 'quantity_in_base_unit' => 18],
            // Clothing variants in WH#1 (Men Shalwar Kameez)
            ['product_id' => 10, 'variant_id' => 6,  'warehouse_id' => 1, 'quantity_in_base_unit' => 25],
            ['product_id' => 10, 'variant_id' => 7,  'warehouse_id' => 1, 'quantity_in_base_unit' => 30],
            ['product_id' => 10, 'variant_id' => 8,  'warehouse_id' => 1, 'quantity_in_base_unit' => 22],
            ['product_id' => 10, 'variant_id' => 9,  'warehouse_id' => 1, 'quantity_in_base_unit' => 18],
            ['product_id' => 10, 'variant_id' => 10, 'warehouse_id' => 1, 'quantity_in_base_unit' => 15],
            // Women Lawn Suit variants in WH#1
            ['product_id' => 11, 'variant_id' => 11, 'warehouse_id' => 1, 'quantity_in_base_unit' => 20],
            ['product_id' => 11, 'variant_id' => 12, 'warehouse_id' => 1, 'quantity_in_base_unit' => 25],
            ['product_id' => 11, 'variant_id' => 13, 'warehouse_id' => 1, 'quantity_in_base_unit' => 20],
            ['product_id' => 11, 'variant_id' => 14, 'warehouse_id' => 1, 'quantity_in_base_unit' => 15],

            // ── Central WH#2 (Karachi) — purchasing stock ─────────────────────
            ['product_id' => 6, 'variant_id' => null, 'warehouse_id' => 2, 'quantity_in_base_unit' => 180],
            ['product_id' => 7, 'variant_id' => null, 'warehouse_id' => 2, 'quantity_in_base_unit' => 120],
            ['product_id' => 8, 'variant_id' => null, 'warehouse_id' => 2, 'quantity_in_base_unit' => 90],

            // ── Branch Store WH#5 (Lahore Main) — ready for POS/Sales ─────────
            ['product_id' => 4,  'variant_id' => 1,    'warehouse_id' => 5, 'quantity_in_base_unit' => 4],   // Samsung A54 Black (5 transferred - 1 sold)
            ['product_id' => 6,  'variant_id' => null,  'warehouse_id' => 5, 'quantity_in_base_unit' => 193], // Flour (200 transferred - 7 sold)
            ['product_id' => 7,  'variant_id' => null,  'warehouse_id' => 5, 'quantity_in_base_unit' => 97],  // Rice (100 transferred - 3 sold)
            ['product_id' => 8,  'variant_id' => null,  'warehouse_id' => 5, 'quantity_in_base_unit' => 49],  // Oil (50 — minus 1 sold)
            ['product_id' => 9,  'variant_id' => null,  'warehouse_id' => 5, 'quantity_in_base_unit' => 48],  // Chana Dal (50 - 2 sold)
            ['product_id' => 12, 'variant_id' => null,  'warehouse_id' => 5, 'quantity_in_base_unit' => 39],  // Pond's Cream (40 transferred - 1 sold)
            ['product_id' => 10, 'variant_id' => 6,     'warehouse_id' => 5, 'quantity_in_base_unit' => 15],  // Men SK Small White
            ['product_id' => 10, 'variant_id' => 7,     'warehouse_id' => 5, 'quantity_in_base_unit' => 20],  // Men SK Medium White
            ['product_id' => 10, 'variant_id' => 8,     'warehouse_id' => 5, 'quantity_in_base_unit' => 12],  // Men SK Large White
            ['product_id' => 10, 'variant_id' => 9,     'warehouse_id' => 5, 'quantity_in_base_unit' => 10],  // Men SK Large Blue
            ['product_id' => 10, 'variant_id' => 10,    'warehouse_id' => 5, 'quantity_in_base_unit' => 8],   // Men SK XL Blue
            ['product_id' => 13, 'variant_id' => null,  'warehouse_id' => 5, 'quantity_in_base_unit' => 30],  // Dove Shampoo

            // ── Branch Store WH#6 (Karachi Center) — ready for POS/Sales ──────
            ['product_id' => 6,  'variant_id' => null,  'warehouse_id' => 6, 'quantity_in_base_unit' => 150], // Flour
            ['product_id' => 7,  'variant_id' => null,  'warehouse_id' => 6, 'quantity_in_base_unit' => 80],  // Rice
            ['product_id' => 11, 'variant_id' => 11,    'warehouse_id' => 6, 'quantity_in_base_unit' => 9],   // Women Suit S-Pink (10 - 1 sold)
            ['product_id' => 11, 'variant_id' => 12,    'warehouse_id' => 6, 'quantity_in_base_unit' => 9],   // Women Suit M-Pink (10 - 1 sold)
            ['product_id' => 11, 'variant_id' => 13,    'warehouse_id' => 6, 'quantity_in_base_unit' => 10],  // Women Suit L-Green
            ['product_id' => 11, 'variant_id' => 14,    'warehouse_id' => 6, 'quantity_in_base_unit' => 10],  // Women Suit XL-Green

            // ── Branch Store WH#7 (Islamabad) — ready for POS/Sales ──────────
            ['product_id' => 5,  'variant_id' => 4,    'warehouse_id' => 7, 'quantity_in_base_unit' => 4],   // Tecno Spark 20 Black (5 - 1 sold pending)
            ['product_id' => 8,  'variant_id' => null,  'warehouse_id' => 7, 'quantity_in_base_unit' => 50],  // Cooking Oil
            ['product_id' => 4,  'variant_id' => 2,    'warehouse_id' => 7, 'quantity_in_base_unit' => 5],   // Samsung A54 Blue 256GB
            ['product_id' => 6,  'variant_id' => null,  'warehouse_id' => 7, 'quantity_in_base_unit' => 80],  // Wheat Flour

            // ── Branch Store WH#8 (Peshawar) — ready for POS/Sales ───────────
            ['product_id' => 9,  'variant_id' => null,  'warehouse_id' => 8, 'quantity_in_base_unit' => 100], // Chana Dal
            ['product_id' => 12, 'variant_id' => null,  'warehouse_id' => 8, 'quantity_in_base_unit' => 25],  // Pond's Cream
            ['product_id' => 13, 'variant_id' => null,  'warehouse_id' => 8, 'quantity_in_base_unit' => 20],  // Dove Shampoo
        ];

        foreach ($stocks as $stock) {
            DB::table('inventory_stocks')->insert(array_merge($stock, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
