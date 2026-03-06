<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // unit IDs from UnitSeeder: 3=Kilogram, 8=Litre, 30=Piece, 31=Dozen, 32=Pack, 33=Box, 36=Bottle
        // category IDs: 1=Electronics,2=Clothing,3=Grocery,4=Home,5=Mobiles,6=Footwear,7=Cosmetics,8=Food
        // sub: 9=Television,10=Refrigerator,11=WashingMachine,12=Men,13=Women,14=Kids,15=Flour/Rice,16=CookingOil,17=Lentils,18=Samsung,19=Apple,20=Tecno

        $products = [
            // Electronics
            [
                'name' => 'Samsung LED TV 43"', 'sku' => 'ELEC-001', 'barcode' => '8801643025564',
                'category_id' => 9, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => false, 'brand' => 'Samsung', 'tax_rate' => 17.00,
                'actual_price' => 85000.00, 'low_stock' => 2, 'track_expiry' => false,
            ],
            [
                'name' => 'Haier 14 CFT Refrigerator', 'sku' => 'ELEC-002', 'barcode' => '8901058009234',
                'category_id' => 10, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => false, 'brand' => 'Haier', 'tax_rate' => 17.00,
                'actual_price' => 65000.00, 'low_stock' => 2, 'track_expiry' => false,
            ],
            [
                'name' => 'Dawlance Twin Tub Washing Machine', 'sku' => 'ELEC-003', 'barcode' => '8901058009567',
                'category_id' => 11, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => false, 'brand' => 'Dawlance', 'tax_rate' => 17.00,
                'actual_price' => 22000.00, 'low_stock' => 3, 'track_expiry' => false,
            ],
            // Mobiles
            [
                'name' => 'Samsung Galaxy A54', 'sku' => 'MOB-001', 'barcode' => '8801643025678',
                'category_id' => 18, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => true, 'brand' => 'Samsung', 'tax_rate' => 17.00,
                'actual_price' => 78000.00, 'low_stock' => 5, 'track_expiry' => false,
            ],
            [
                'name' => 'Tecno Spark 20', 'sku' => 'MOB-002', 'barcode' => '6971793005432',
                'category_id' => 20, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => true, 'brand' => 'Tecno', 'tax_rate' => 17.00,
                'actual_price' => 28000.00, 'low_stock' => 5, 'track_expiry' => false,
            ],
            // Grocery
            [
                'name' => 'Roz Wheat Flour (10 kg)', 'sku' => 'GRO-001', 'barcode' => '8901058001111',
                'category_id' => 15, 'base_unit_id' => 3, 'default_display_unit_id' => 32,
                'has_variants' => false, 'brand' => 'Roz', 'tax_rate' => 0.00,
                'actual_price' => 1350.00, 'low_stock' => 20, 'track_expiry' => true,
            ],
            [
                'name' => 'Bright Basmati Rice (5 kg)', 'sku' => 'GRO-002', 'barcode' => '8901058002222',
                'category_id' => 15, 'base_unit_id' => 3, 'default_display_unit_id' => 32,
                'has_variants' => false, 'brand' => 'Bright', 'tax_rate' => 0.00,
                'actual_price' => 1100.00, 'low_stock' => 20, 'track_expiry' => true,
            ],
            [
                'name' => 'Crispy Cooking Oil (5 Litre)', 'sku' => 'GRO-003', 'barcode' => '8901058003333',
                'category_id' => 16, 'base_unit_id' => 8, 'default_display_unit_id' => 36,
                'has_variants' => false, 'brand' => 'Crispy', 'tax_rate' => 0.00,
                'actual_price' => 2400.00, 'low_stock' => 15, 'track_expiry' => true,
            ],
            [
                'name' => 'Chana Dal - Chickpea Lentils (1 kg)', 'sku' => 'GRO-004', 'barcode' => '8901058004444',
                'category_id' => 17, 'base_unit_id' => 3, 'default_display_unit_id' => 32,
                'has_variants' => false, 'brand' => 'Local', 'tax_rate' => 0.00,
                'actual_price' => 380.00, 'low_stock' => 30, 'track_expiry' => true,
            ],
            // Clothing
            [
                'name' => 'Cotton Shalwar Kameez (Men)', 'sku' => 'CLO-001', 'barcode' => '8901058005555',
                'category_id' => 12, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => true, 'brand' => 'Khaadi', 'tax_rate' => 10.00,
                'actual_price' => 3500.00, 'low_stock' => 10, 'track_expiry' => false,
            ],
            [
                'name' => 'Lawn Suit (Women 3-Piece)', 'sku' => 'CLO-002', 'barcode' => '8901058006666',
                'category_id' => 13, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => true, 'brand' => 'Gul Ahmed', 'tax_rate' => 10.00,
                'actual_price' => 4200.00, 'low_stock' => 10, 'track_expiry' => false,
            ],
            // Cosmetics
            [
                'name' => "Pond's Cream (100g)", 'sku' => 'COS-001', 'barcode' => '8901058007777',
                'category_id' => 7, 'base_unit_id' => 30, 'default_display_unit_id' => 30,
                'has_variants' => false, 'brand' => "Pond's", 'tax_rate' => 5.00,
                'actual_price' => 350.00, 'low_stock' => 20, 'track_expiry' => true,
            ],
            [
                'name' => 'Dove Shampoo (400ml)', 'sku' => 'COS-002', 'barcode' => '8901058008888',
                'category_id' => 7, 'base_unit_id' => 8, 'default_display_unit_id' => 36,
                'has_variants' => false, 'brand' => 'Dove', 'tax_rate' => 5.00,
                'actual_price' => 650.00, 'low_stock' => 20, 'track_expiry' => true,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert(array_merge($product, [
                'product_img' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));
        }

        // Product Variants for Samsung Galaxy A54 (product_id = 4)
        DB::table('product_variants')->insert([
            ['product_id' => 4, 'variant_name' => '6GB/128GB - Black',  'sku' => 'MOB-001-BLK-128', 'barcode' => '8801643025679', 'actual_price' => 78000.00, 'color' => 'Black',  'size' => '128GB', 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 4, 'variant_name' => '8GB/256GB - Blue',   'sku' => 'MOB-001-BLU-256', 'barcode' => '8801643025680', 'actual_price' => 92000.00, 'color' => 'Blue',   'size' => '256GB', 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 4, 'variant_name' => '8GB/256GB - Silver', 'sku' => 'MOB-001-SLV-256', 'barcode' => '8801643025681', 'actual_price' => 92000.00, 'color' => 'Silver', 'size' => '256GB', 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Product Variants for Tecno Spark 20 (product_id = 5)
        DB::table('product_variants')->insert([
            ['product_id' => 5, 'variant_name' => '8GB/128GB - Black', 'sku' => 'MOB-002-BLK-128', 'barcode' => '6971793005433', 'actual_price' => 28000.00, 'color' => 'Black', 'size' => '128GB', 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 5, 'variant_name' => '8GB/128GB - Gold',  'sku' => 'MOB-002-GLD-128', 'barcode' => '6971793005434', 'actual_price' => 28000.00, 'color' => 'Gold',  'size' => '128GB', 'low_stock' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Clothing Variants for Men Cotton Shalwar Kameez (product_id = 10)
        DB::table('product_variants')->insert([
            ['product_id' => 10, 'variant_name' => 'Small - White',  'sku' => 'CLO-001-S-WHT', 'barcode' => null, 'actual_price' => 3500.00, 'color' => 'White', 'size' => 'S',  'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 10, 'variant_name' => 'Medium - White', 'sku' => 'CLO-001-M-WHT', 'barcode' => null, 'actual_price' => 3500.00, 'color' => 'White', 'size' => 'M',  'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 10, 'variant_name' => 'Large - White',  'sku' => 'CLO-001-L-WHT', 'barcode' => null, 'actual_price' => 3500.00, 'color' => 'White', 'size' => 'L',  'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 10, 'variant_name' => 'Large - Blue',   'sku' => 'CLO-001-L-BLU', 'barcode' => null, 'actual_price' => 3500.00, 'color' => 'Blue',  'size' => 'L',  'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 10, 'variant_name' => 'XL - Blue',      'sku' => 'CLO-001-XL-BLU','barcode' => null, 'actual_price' => 3500.00, 'color' => 'Blue',  'size' => 'XL', 'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Women Lawn Suit Variants (product_id = 11)
        DB::table('product_variants')->insert([
            ['product_id' => 11, 'variant_name' => 'S - Pink',   'sku' => 'CLO-002-S-PNK', 'barcode' => null, 'actual_price' => 4200.00, 'color' => 'Pink',  'size' => 'S', 'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 11, 'variant_name' => 'M - Pink',   'sku' => 'CLO-002-M-PNK', 'barcode' => null, 'actual_price' => 4200.00, 'color' => 'Pink',  'size' => 'M', 'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 11, 'variant_name' => 'L - Green',  'sku' => 'CLO-002-L-GRN', 'barcode' => null, 'actual_price' => 4200.00, 'color' => 'Green', 'size' => 'L', 'low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => 11, 'variant_name' => 'XL - Green', 'sku' => 'CLO-002-XL-GRN','barcode' => null, 'actual_price' => 4200.00, 'color' => 'Green', 'size' => 'XL','low_stock' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
