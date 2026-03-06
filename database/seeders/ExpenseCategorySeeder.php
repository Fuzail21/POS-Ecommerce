<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('expense_categories')->insert([
            ['name' => 'Rent',               'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Electricity',         'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Water',               'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salaries',            'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transport',           'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Repair & Maintenance','created_at' => now(), 'updated_at' => now()],
            ['name' => 'Advertising',         'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Stationery',          'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Internet & Phone',    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Miscellaneous',       'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
