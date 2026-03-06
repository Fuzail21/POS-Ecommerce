<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountRuleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('discount_rules')->insert([
            [
                'name'        => 'Grocery Sale 10%',
                'type'        => 'category',
                'target_ids'  => json_encode([3]),  // Grocery category
                'discount'    => 10.00,
                'coupon_code' => null,
                'start_date'  => '2026-02-01',
                'end_date'    => '2026-02-28',
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Mobile Special Offer 5%',
                'type'        => 'category',
                'target_ids'  => json_encode([5]),  // Mobile category
                'discount'    => 5.00,
                'coupon_code' => null,
                'start_date'  => '2026-02-15',
                'end_date'    => '2026-03-15',
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Eid Coupon EID25',
                'type'        => 'coupon',
                'target_ids'  => null,
                'discount'    => 25.00,
                'coupon_code' => 'EID25',
                'start_date'  => '2026-03-28',
                'end_date'    => '2026-04-05',
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => 'Welcome Discount WELCOME10',
                'type'        => 'coupon',
                'target_ids'  => null,
                'discount'    => 10.00,
                'coupon_code' => 'WELCOME10',
                'start_date'  => '2026-01-01',
                'end_date'    => '2026-12-31',
                'created_at'  => now(), 'updated_at' => now(),
            ],
            [
                'name'        => "Pond's Cream Special",
                'type'        => 'product',
                'target_ids'  => json_encode([12]),  // Pond's Cream product_id
                'discount'    => 15.00,
                'coupon_code' => null,
                'start_date'  => '2026-02-01',
                'end_date'    => '2026-02-28',
                'created_at'  => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
