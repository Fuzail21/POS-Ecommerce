<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElectronicsProductSeeder extends Seeder
{
    /**
     * Seed 5 new electronics products.
     *
     * Category IDs (from CategorySeeder):
     *   1  = Electronics (parent)
     *   9  = ٹیلی ویژن (TV)
     *   10 = فریج (Fridge)
     *   11 = واشنگ مشین (Washing Machine)
     *
     * Unit IDs (from UnitSeeder):
     *   30 = Piece
     */
    public function run(): void
    {
        // ── 1. Sony Bravia 55" 4K UHD Smart TV ─────────────────────────── simple
        DB::table('products')->insert([
            'name'                    => 'Sony Bravia 55" 4K UHD Smart TV',
            'sku'                     => 'ELEC-004',
            'barcode'                 => '4901780977925',
            'category_id'             => 9,
            'base_unit_id'            => 30,
            'default_display_unit_id' => 30,
            'has_variants'            => false,
            'brand'                   => 'Sony',
            'tax_rate'                => 17.00,
            'actual_price'            => 195000.00,
            'low_stock'               => 2,
            'track_expiry'            => false,
            'product_img'             => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // ── 2. Orient HD Inverter Split AC — with variants (1 Ton / 1.5 Ton / 2 Ton)
        $acId = DB::table('products')->insertGetId([
            'name'                    => 'Orient HD Inverter Split AC',
            'sku'                     => 'ELEC-005',
            'barcode'                 => '8901058011001',
            'category_id'             => 1,
            'base_unit_id'            => 30,
            'default_display_unit_id' => 30,
            'has_variants'            => true,
            'brand'                   => 'Orient',
            'tax_rate'                => 17.00,
            'actual_price'            => 110000.00,
            'low_stock'               => 2,
            'track_expiry'            => false,
            'product_img'             => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        DB::table('product_variants')->insert([
            ['product_id' => $acId, 'variant_name' => '1 Ton - White',   'sku' => 'ELEC-005-1T',   'barcode' => '8901058011002', 'actual_price' =>  90000.00, 'color' => 'White', 'size' => '1 Ton',   'low_stock' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $acId, 'variant_name' => '1.5 Ton - White', 'sku' => 'ELEC-005-15T',  'barcode' => '8901058011003', 'actual_price' => 110000.00, 'color' => 'White', 'size' => '1.5 Ton', 'low_stock' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $acId, 'variant_name' => '2 Ton - White',   'sku' => 'ELEC-005-2T',   'barcode' => '8901058011004', 'actual_price' => 140000.00, 'color' => 'White', 'size' => '2 Ton',   'low_stock' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 3. Kenwood Microwave Oven 30L ──────────────────────────────── simple
        DB::table('products')->insert([
            'name'                    => 'Kenwood Microwave Oven 30L',
            'sku'                     => 'ELEC-006',
            'barcode'                 => '5011423120231',
            'category_id'             => 1,
            'base_unit_id'            => 30,
            'default_display_unit_id' => 30,
            'has_variants'            => false,
            'brand'                   => 'Kenwood',
            'tax_rate'                => 17.00,
            'actual_price'            => 28500.00,
            'low_stock'               => 3,
            'track_expiry'            => false,
            'product_img'             => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // ── 4. Dell Inspiron Laptop — with variants (i5 / i7)
        $laptopId = DB::table('products')->insertGetId([
            'name'                    => 'Dell Inspiron 15 Laptop',
            'sku'                     => 'ELEC-007',
            'barcode'                 => '5397184728092',
            'category_id'             => 1,
            'base_unit_id'            => 30,
            'default_display_unit_id' => 30,
            'has_variants'            => true,
            'brand'                   => 'Dell',
            'tax_rate'                => 17.00,
            'actual_price'            => 145000.00,
            'low_stock'               => 2,
            'track_expiry'            => false,
            'product_img'             => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        DB::table('product_variants')->insert([
            ['product_id' => $laptopId, 'variant_name' => 'i5 / 8GB / 512GB SSD - Silver', 'sku' => 'ELEC-007-I5-SLV', 'barcode' => '5397184728093', 'actual_price' => 145000.00, 'color' => 'Silver', 'size' => 'i5',  'low_stock' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $laptopId, 'variant_name' => 'i7 / 16GB / 512GB SSD - Silver','sku' => 'ELEC-007-I7-SLV', 'barcode' => '5397184728094', 'actual_price' => 195000.00, 'color' => 'Silver', 'size' => 'i7',  'low_stock' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $laptopId, 'variant_name' => 'i7 / 16GB / 1TB SSD - Black',  'sku' => 'ELEC-007-I7-BLK', 'barcode' => '5397184728095', 'actual_price' => 215000.00, 'color' => 'Black',  'size' => 'i7',  'low_stock' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── 5. JBL Portable Bluetooth Speaker — with variants (colors)
        $jblId = DB::table('products')->insertGetId([
            'name'                    => 'JBL Flip 6 Portable Bluetooth Speaker',
            'sku'                     => 'ELEC-008',
            'barcode'                 => '6925281986451',
            'category_id'             => 1,
            'base_unit_id'            => 30,
            'default_display_unit_id' => 30,
            'has_variants'            => true,
            'brand'                   => 'JBL',
            'tax_rate'                => 17.00,
            'actual_price'            => 22000.00,
            'low_stock'               => 5,
            'track_expiry'            => false,
            'product_img'             => null,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        DB::table('product_variants')->insert([
            ['product_id' => $jblId, 'variant_name' => 'Black',  'sku' => 'ELEC-008-BLK', 'barcode' => '6925281986452', 'actual_price' => 22000.00, 'color' => 'Black',  'size' => null, 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $jblId, 'variant_name' => 'Blue',   'sku' => 'ELEC-008-BLU', 'barcode' => '6925281986453', 'actual_price' => 22000.00, 'color' => 'Blue',   'size' => null, 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $jblId, 'variant_name' => 'Red',    'sku' => 'ELEC-008-RED', 'barcode' => '6925281986454', 'actual_price' => 22000.00, 'color' => 'Red',    'size' => null, 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $jblId, 'variant_name' => 'Teal',   'sku' => 'ELEC-008-TEL', 'barcode' => '6925281986455', 'actual_price' => 22000.00, 'color' => 'Teal',   'size' => null, 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
