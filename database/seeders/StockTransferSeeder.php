<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransferSeeder extends Seeder
{
    public function run(): void
    {
        // Transfer 1 — WH#1 (Lahore Central) → WH#5 (Lahore Main Branch Store)
        // Products: Wheat Flour 200, Basmati Rice 100, Samsung A54 Black 5, Pond's Cream 40
        DB::table('stock_transfers')->insert([
            ['from_warehouse_id' => 1, 'to_warehouse_id' => 5, 'product_id' => 6,  'variant_id' => null, 'quantity' => 200, 'transfer_reference' => 'TRF-2026-0001-A', 'notes' => 'Lahore branch grocery restock', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['from_warehouse_id' => 1, 'to_warehouse_id' => 5, 'product_id' => 7,  'variant_id' => null, 'quantity' => 100, 'transfer_reference' => 'TRF-2026-0001-B', 'notes' => 'Lahore branch rice restock',    'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['from_warehouse_id' => 1, 'to_warehouse_id' => 5, 'product_id' => 4,  'variant_id' => 1,    'quantity' => 5,   'transfer_reference' => 'TRF-2026-0001-C', 'notes' => 'Lahore branch mobile stock',   'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['from_warehouse_id' => 1, 'to_warehouse_id' => 5, 'product_id' => 12, 'variant_id' => null, 'quantity' => 40,  'transfer_reference' => 'TRF-2026-0001-D', 'notes' => 'Lahore branch cosmetics',      'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Transfer 2 — WH#2 (Karachi Storage) → WH#6 (Karachi Center Branch Store)
        DB::table('stock_transfers')->insert([
            ['from_warehouse_id' => 2, 'to_warehouse_id' => 6, 'product_id' => 6, 'variant_id' => null, 'quantity' => 150, 'transfer_reference' => 'TRF-2026-0002-A', 'notes' => 'Karachi branch wheat flour restock', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['from_warehouse_id' => 2, 'to_warehouse_id' => 6, 'product_id' => 7, 'variant_id' => null, 'quantity' =>  80, 'transfer_reference' => 'TRF-2026-0002-B', 'notes' => 'Karachi branch rice restock',        'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Transfer 3 — WH#1 → WH#7 (Islamabad Branch Store)
        DB::table('stock_transfers')->insert([
            ['from_warehouse_id' => 1, 'to_warehouse_id' => 7, 'product_id' => 5, 'variant_id' => 4, 'quantity' => 5, 'transfer_reference' => 'TRF-2026-0003-A', 'notes' => 'Islamabad branch Tecno mobile stock', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['from_warehouse_id' => 1, 'to_warehouse_id' => 7, 'product_id' => 8, 'variant_id' => null, 'quantity' => 50, 'transfer_reference' => 'TRF-2026-0003-B', 'notes' => 'Islamabad cooking oil transfer',    'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
