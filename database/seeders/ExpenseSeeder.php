<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('expenses')->insert([
            ['branch_id' => 1, 'category_id' => 1, 'amount' => 85000.00,  'description' => 'Monthly shop rent - Lahore Main Branch',       'expense_date' => '2026-02-01', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 2, 'amount' => 18500.00,  'description' => 'Electricity bill - February',                  'expense_date' => '2026-02-05', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 4, 'amount' => 95000.00,  'description' => 'Staff salaries - January',                     'expense_date' => '2026-02-03', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 5, 'amount' => 5500.00,   'description' => 'Goods delivery transport charges',             'expense_date' => '2026-02-08', 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 1, 'amount' => 120000.00, 'description' => 'Monthly shop rent - Karachi Center Branch',    'expense_date' => '2026-02-01', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 2, 'category_id' => 2, 'amount' => 22000.00,  'description' => 'Electricity bill Karachi - February',          'expense_date' => '2026-02-06', 'created_by' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 7, 'amount' => 15000.00,  'description' => 'Social media advertising campaign',            'expense_date' => '2026-02-10', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 6, 'amount' => 3200.00,   'description' => 'AC service and repair',                        'expense_date' => '2026-02-12', 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 3, 'category_id' => 1, 'amount' => 75000.00,  'description' => 'Monthly shop rent - Islamabad Branch',         'expense_date' => '2026-02-01', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'category_id' => 9, 'amount' => 3500.00,   'description' => 'Internet and phone bill',                      'expense_date' => '2026-02-15', 'created_by' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
