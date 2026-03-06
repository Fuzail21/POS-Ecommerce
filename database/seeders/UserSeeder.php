<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // Admin
            [
                'name'              => 'Ahmad Raza',
                'email'             => 'admin@pos.pk',
                'password'          => Hash::make('admin123'),
                'status'            => 'Active',
                'role_id'           => 1, // Admin
                'branch_id'         => 1,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Manager
            [
                'name'              => 'Bilal Hussain',
                'email'             => 'manager@pos.pk',
                'password'          => Hash::make('manager123'),
                'status'            => 'Active',
                'role_id'           => 2, // Manager
                'branch_id'         => 1,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Cashier 1
            [
                'name'              => 'Sana Malik',
                'email'             => 'cashier@pos.pk',
                'password'          => Hash::make('cashier123'),
                'status'            => 'Active',
                'role_id'           => 3, // Cashier
                'branch_id'         => 1,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Accountant
            [
                'name'              => 'Zubair Khan',
                'email'             => 'accountant@pos.pk',
                'password'          => Hash::make('account123'),
                'status'            => 'Active',
                'role_id'           => 4, // Accountant
                'branch_id'         => 2,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Inventory
            [
                'name'              => 'Usman Ali',
                'email'             => 'inventory@pos.pk',
                'password'          => Hash::make('inventory123'),
                'status'            => 'Active',
                'role_id'           => 5, // Inventory
                'branch_id'         => 3,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Cashier Karachi
            [
                'name'              => 'Fatima Noor',
                'email'             => 'cashier.khi@pos.pk',
                'password'          => Hash::make('cashier123'),
                'status'            => 'Active',
                'role_id'           => 3, // Cashier
                'branch_id'         => 2,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
