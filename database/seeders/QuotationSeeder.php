<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        // Columns: order_tax_amount (renamed from order_tax_percentage), tax_percentage (new)

        // Quotation 1 — Pending: Electronics for Ali Ahmad (branch 1)
        DB::table('quotations')->insert([
            'quotation_number'   => 'QUO-2026-0001',
            'customer_id'        => 2, // Ali Ahmad Khan
            'branch_id'          => 1, // Lahore Main Branch
            'quotation_date'     => '2026-02-05',
            'order_tax_amount'   => 14450.00,
            'tax_percentage'     => 17.00,
            'discount_percentage'=> 0.00,
            'discount_type'      => 'fixed',
            'shipping_cost'      => 0.00,
            'grand_total'        => 99450.00,
            'status'             => 'pending',
            'note'               => 'Customer enquired about Samsung TV - requires 17% tax',
            'created_at'         => now(), 'updated_at' => now(),
        ]);
        DB::table('quotation_items')->insert([
            ['quotation_id' => 1, 'product_id' => 1, 'product_variant_id' => null, 'unit_price' => 85000.00, 'quantity' => 1, 'discount_amount' => 0.00, 'tax_amount' => 14450.00, 'subtotal' => 99450.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Quotation 2 — Sent: Grocery bulk order for Hassan Malik (branch 1)
        DB::table('quotations')->insert([
            'quotation_number'   => 'QUO-2026-0002',
            'customer_id'        => 6, // Hassan Malik
            'branch_id'          => 1, // Lahore Main Branch
            'quotation_date'     => '2026-02-08',
            'order_tax_amount'   => 0.00,
            'tax_percentage'     => 0.00,
            'discount_percentage'=> 10.00,
            'discount_type'      => 'percentage',
            'shipping_cost'      => 0.00,
            'grand_total'        => 19350.00,
            'status'             => 'sent',
            'note'               => 'Bulk grocery order - 10% discount applied',
            'created_at'         => now(), 'updated_at' => now(),
        ]);
        DB::table('quotation_items')->insert([
            ['quotation_id' => 2, 'product_id' => 6, 'product_variant_id' => null, 'unit_price' => 1350.00, 'quantity' => 10, 'discount_amount' => 1350.00, 'tax_amount' => 0.00, 'subtotal' => 12150.00, 'created_at' => now(), 'updated_at' => now()],
            ['quotation_id' => 2, 'product_id' => 7, 'product_variant_id' => null, 'unit_price' => 1100.00, 'quantity' => 5,  'discount_amount' =>  550.00, 'tax_amount' => 0.00, 'subtotal' =>  4950.00, 'created_at' => now(), 'updated_at' => now()],
            ['quotation_id' => 2, 'product_id' => 8, 'product_variant_id' => null, 'unit_price' => 2400.00, 'quantity' => 1,  'discount_amount' =>  240.00, 'tax_amount' => 0.00, 'subtotal' =>  2160.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Quotation 3 — Accepted: Mobile phones for Ayesha Siddiqui (branch 2)
        DB::table('quotations')->insert([
            'quotation_number'   => 'QUO-2026-0003',
            'customer_id'        => 3, // Ayesha Siddiqui
            'branch_id'          => 2, // Karachi Center Branch
            'quotation_date'     => '2026-02-12',
            'order_tax_amount'   => 9044.00,
            'tax_percentage'     => 17.00,
            'discount_percentage'=> 5.00,
            'discount_type'      => 'percentage',
            'shipping_cost'      => 250.00,
            'grand_total'        => 56394.00,
            'status'             => 'accepted',
            'note'               => 'Two Tecno Spark 20 phones with 5% mobile discount',
            'created_at'         => now(), 'updated_at' => now(),
        ]);
        DB::table('quotation_items')->insert([
            ['quotation_id' => 3, 'product_id' => 5, 'product_variant_id' => 4, 'unit_price' => 28000.00, 'quantity' => 2, 'discount_amount' => 2800.00, 'tax_amount' => 9044.00, 'subtotal' => 56144.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Quotation 4 — Pending: Clothing for Muhammad Umar (branch 3)
        DB::table('quotations')->insert([
            'quotation_number'   => 'QUO-2026-0004',
            'customer_id'        => 4, // Muhammad Umar Farooq
            'branch_id'          => 3, // Islamabad Branch
            'quotation_date'     => '2026-02-18',
            'order_tax_amount'   => 700.00,
            'tax_percentage'     => 10.00,
            'discount_percentage'=> 0.00,
            'discount_type'      => 'fixed',
            'shipping_cost'      => 300.00,
            'grand_total'        => 8300.00,
            'status'             => 'pending',
            'note'               => 'Men Shalwar Kameez - Large size, Blue color',
            'created_at'         => now(), 'updated_at' => now(),
        ]);
        DB::table('quotation_items')->insert([
            ['quotation_id' => 4, 'product_id' => 10, 'product_variant_id' => 9, 'unit_price' => 3500.00, 'quantity' => 2, 'discount_amount' => 0.00, 'tax_amount' => 700.00, 'subtotal' => 7700.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Quotation 5 — Rejected: Cosmetics for Rabia Khan (branch 4)
        DB::table('quotations')->insert([
            'quotation_number'   => 'QUO-2026-0005',
            'customer_id'        => 5, // Rabia Khan
            'branch_id'          => 4, // Peshawar Branch
            'quotation_date'     => '2026-02-20',
            'order_tax_amount'   => 44.63,
            'tax_percentage'     => 5.00,
            'discount_percentage'=> 15.00,
            'discount_type'      => 'percentage',
            'shipping_cost'      => 0.00,
            'grand_total'        => 937.50,
            'status'             => 'rejected',
            'note'               => 'Customer rejected due to delivery time',
            'created_at'         => now(), 'updated_at' => now(),
        ]);
        DB::table('quotation_items')->insert([
            ['quotation_id' => 5, 'product_id' => 12, 'product_variant_id' => null, 'unit_price' => 350.00, 'quantity' => 3, 'discount_amount' => 157.50, 'tax_amount' => 44.63, 'subtotal' => 892.13, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
