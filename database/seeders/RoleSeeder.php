<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Admin',     'description' => 'Full system access',          'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager',   'description' => 'Branch manager access',        'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cashier',   'description' => 'POS sales and cash register',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accountant','description' => 'Financial reports and payments','created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inventory', 'description' => 'Stock and warehouse management','created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
