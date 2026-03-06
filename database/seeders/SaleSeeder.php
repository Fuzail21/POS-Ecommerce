<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        // Sale 1 - POS Cash Sale
        DB::table('sales')->insert([
            'customer_id'      => 1, // Walk-in
            'branch_id'        => 1,
            'invoice_number'   => 'INV-2026-0001',
            'sale_date'        => '2026-02-10',
            'total_amount'     => 3730.00,
            'discount_amount'  => 0.00,
            'tax_amount'       => 0.00,
            'tax_percentage'   => 0.00,
            'final_amount'     => 3730.00,
            'paid_amount'      => 3730.00,
            'due_amount'       => 0.00,
            'payment_method'   => 'cash',
            'shipping'         => 0.00,
            'sale_origin'      => 'POS',
            'status'           => 'completed',
            'created_by'       => 3,
            'created_at'       => now(), 'updated_at' => now(),
        ]);
        DB::table('sale_items')->insert([
            ['sale_id' => 1, 'product_id' => 6, 'variant_id' => null, 'quantity' => 2, 'unit_id' => 32, 'quantity_in_base_unit' => 2, 'unit_price' => 1350.00, 'discount' => 0, 'tax' => 0, 'total_price' => 2700.00, 'created_at' => now(), 'updated_at' => now()],
            ['sale_id' => 1, 'product_id' => 9, 'variant_id' => null, 'quantity' => 2, 'unit_id' => 32, 'quantity_in_base_unit' => 2, 'unit_price' => 380.00,  'discount' => 0, 'tax' => 0, 'total_price' => 760.00,  'created_at' => now(), 'updated_at' => now()],
            ['sale_id' => 1, 'product_id' => 12,'variant_id' => null, 'quantity' => 1, 'unit_id' => 30, 'quantity_in_base_unit' => 1, 'unit_price' => 350.00,  'discount' => 0, 'tax' => 0, 'total_price' => 350.00,  'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sale 2 - Mobile sale to customer Ali Ahmad
        DB::table('sales')->insert([
            'customer_id'      => 2, // Ali Ahmad
            'branch_id'        => 1,
            'invoice_number'   => 'INV-2026-0002',
            'sale_date'        => '2026-02-11',
            'total_amount'     => 78000.00,
            'discount_amount'  => 3900.00,
            'tax_amount'       => 12580.00,
            'tax_percentage'   => 17.00,
            'final_amount'     => 86680.00,
            'paid_amount'      => 86680.00,
            'due_amount'       => 0.00,
            'payment_method'   => 'card',
            'shipping'         => 0.00,
            'sale_origin'      => 'POS',
            'status'           => 'completed',
            'created_by'       => 3,
            'created_at'       => now(), 'updated_at' => now(),
        ]);
        DB::table('sale_items')->insert([
            ['sale_id' => 2, 'product_id' => 4, 'variant_id' => 1, 'quantity' => 1, 'unit_id' => 30, 'quantity_in_base_unit' => 1, 'unit_price' => 78000.00, 'discount' => 3900.00, 'tax' => 12580.00, 'total_price' => 86680.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sale 3 - E-commerce order from Ayesha
        DB::table('sales')->insert([
            'customer_id'      => 3, // Ayesha
            'branch_id'        => 2,
            'invoice_number'   => 'INV-2026-0003',
            'sale_date'        => '2026-02-12',
            'total_amount'     => 8400.00,
            'discount_amount'  => 0.00,
            'tax_amount'       => 840.00,
            'tax_percentage'   => 10.00,
            'final_amount'     => 9490.00,
            'paid_amount'      => 9490.00,
            'due_amount'       => 0.00,
            'payment_method'   => 'online',
            'shipping'         => 250.00,
            'sale_origin'      => 'E-commerce',
            'status'           => 'completed',
            'created_by'       => 6,
            'created_at'       => now(), 'updated_at' => now(),
        ]);
        DB::table('sale_items')->insert([
            ['sale_id' => 3, 'product_id' => 11, 'variant_id' => 11, 'quantity' => 1, 'unit_id' => 30, 'quantity_in_base_unit' => 1, 'unit_price' => 4200.00, 'discount' => 0, 'tax' => 420.00,  'total_price' => 4620.00, 'created_at' => now(), 'updated_at' => now()],
            ['sale_id' => 3, 'product_id' => 11, 'variant_id' => 12, 'quantity' => 1, 'unit_id' => 30, 'quantity_in_base_unit' => 1, 'unit_price' => 4200.00, 'discount' => 0, 'tax' => 420.00,  'total_price' => 4620.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sale 4 - Grocery bulk sale to Hassan Malik
        DB::table('sales')->insert([
            'customer_id'      => 6, // Hassan Malik
            'branch_id'        => 1,
            'invoice_number'   => 'INV-2026-0004',
            'sale_date'        => '2026-02-14',
            'total_amount'     => 11300.00,
            'discount_amount'  => 1130.00,
            'tax_amount'       => 0.00,
            'tax_percentage'   => 0.00,
            'final_amount'     => 10170.00,
            'paid_amount'      => 10170.00,
            'due_amount'       => 0.00,
            'payment_method'   => 'cash',
            'shipping'         => 0.00,
            'sale_origin'      => 'POS',
            'status'           => 'completed',
            'created_by'       => 3,
            'created_at'       => now(), 'updated_at' => now(),
        ]);
        DB::table('sale_items')->insert([
            ['sale_id' => 4, 'product_id' => 6, 'variant_id' => null, 'quantity' => 5, 'unit_id' => 32, 'quantity_in_base_unit' => 5, 'unit_price' => 1350.00, 'discount' => 675.00,  'tax' => 0, 'total_price' => 6075.00, 'created_at' => now(), 'updated_at' => now()],
            ['sale_id' => 4, 'product_id' => 7, 'variant_id' => null, 'quantity' => 3, 'unit_id' => 32, 'quantity_in_base_unit' => 3, 'unit_price' => 1100.00, 'discount' => 330.00,  'tax' => 0, 'total_price' => 2970.00, 'created_at' => now(), 'updated_at' => now()],
            ['sale_id' => 4, 'product_id' => 8, 'variant_id' => null, 'quantity' => 1, 'unit_id' => 36, 'quantity_in_base_unit' => 1, 'unit_price' => 2400.00, 'discount' => 240.00,  'tax' => 0, 'total_price' => 2160.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sale 5 - Pending e-commerce order
        DB::table('sales')->insert([
            'customer_id'      => 4, // Umar
            'branch_id'        => 3,
            'invoice_number'   => 'INV-2026-0005',
            'sale_date'        => '2026-02-20',
            'total_amount'     => 28000.00,
            'discount_amount'  => 0.00,
            'tax_amount'       => 4760.00,
            'tax_percentage'   => 17.00,
            'final_amount'     => 32810.00,
            'paid_amount'      => 0.00,
            'due_amount'       => 32810.00,
            'payment_method'   => 'online',
            'shipping'         => 50.00,
            'sale_origin'      => 'E-commerce',
            'status'           => 'pending',
            'created_by'       => 6,
            'created_at'       => now(), 'updated_at' => now(),
        ]);
        DB::table('sale_items')->insert([
            ['sale_id' => 5, 'product_id' => 5, 'variant_id' => 4, 'quantity' => 1, 'unit_id' => 30, 'quantity_in_base_unit' => 1, 'unit_price' => 28000.00, 'discount' => 0, 'tax' => 4760.00, 'total_price' => 32760.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
