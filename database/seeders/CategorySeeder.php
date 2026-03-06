<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Parent categories (id 1–8)
        DB::table('categories')->insert([
            ['name' => 'Electronics',   'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Clothing',      'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grocery',       'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Home & Living', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mobiles',       'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Footwear',      'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cosmetics',     'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Food & Snacks', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Sub-categories
        DB::table('categories')->insert([
            // Electronics (1)
            ['name' => 'Television',      'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()], // id 9
            ['name' => 'Refrigerator',    'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()], // id 10
            ['name' => 'Washing Machine', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()], // id 11
            // Clothing (2)
            ['name' => 'Men Clothing',    'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()], // id 12
            ['name' => 'Women Clothing',  'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()], // id 13
            ['name' => 'Kids Clothing',   'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()], // id 14
            // Grocery (3)
            ['name' => 'Flour & Rice',    'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()], // id 15
            ['name' => 'Cooking Oil',     'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()], // id 16
            ['name' => 'Lentils & Beans', 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()], // id 17
            // Mobiles (5)
            ['name' => 'Samsung',         'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()], // id 18
            ['name' => 'Apple',           'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()], // id 19
            ['name' => 'Tecno',           'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()], // id 20
        ]);
    }
}
