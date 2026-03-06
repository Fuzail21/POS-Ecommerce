<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'name'       => 'Walk-in Customer',
                'last_name'  => null,
                'phone'      => '0000-0000000',
                'email'      => null,
                'address'    => null,
                'balance'    => 0,
                'country'    => 'Pakistan',
                'city'       => null,
                'postcode'   => null,
                'password'   => null,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name'       => 'Ali Ahmad',
                'last_name'  => 'Khan',
                'phone'      => '0300-1234567',
                'email'      => 'ali.ahmad@gmail.com',
                'address'    => 'House 12, Gulberg III, Lahore',
                'balance'    => 0,
                'country'    => 'Pakistan',
                'city'       => 'Lahore',
                'postcode'   => '54000',
                'password'   => Hash::make('customer123'),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name'       => 'Ayesha',
                'last_name'  => 'Siddiqui',
                'phone'      => '0301-9876543',
                'email'      => 'ayesha.siddiqui@gmail.com',
                'address'    => 'Flat 5, Block C, DHA Phase 2, Karachi',
                'balance'    => 2500,
                'country'    => 'Pakistan',
                'city'       => 'Karachi',
                'postcode'   => '75500',
                'password'   => Hash::make('customer123'),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name'       => 'Muhammad Umar',
                'last_name'  => 'Farooq',
                'phone'      => '0312-5556677',
                'email'      => 'umar.farooq@hotmail.com',
                'address'    => 'House 7, Street 3, E-7, Islamabad',
                'balance'    => 0,
                'country'    => 'Pakistan',
                'city'       => 'Islamabad',
                'postcode'   => '44000',
                'password'   => Hash::make('customer123'),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name'       => 'Rabia',
                'last_name'  => 'Khan',
                'phone'      => '0333-4445566',
                'email'      => 'rabia.khan@gmail.com',
                'address'    => 'University Road, Peshawar',
                'balance'    => 1200,
                'country'    => 'Pakistan',
                'city'       => 'Peshawar',
                'postcode'   => '25000',
                'password'   => Hash::make('customer123'),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name'       => 'Hassan',
                'last_name'  => 'Malik',
                'phone'      => '0321-7778899',
                'email'      => 'hassan.malik@gmail.com',
                'address'    => 'Johar Town, Lahore',
                'balance'    => 0,
                'country'    => 'Pakistan',
                'city'       => 'Lahore',
                'postcode'   => '54770',
                'password'   => Hash::make('customer123'),
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'name'       => 'Samina',
                'last_name'  => 'Begum',
                'phone'      => '0345-1122334',
                'email'      => 'samina.begum@yahoo.com',
                'address'    => 'Saddar, Rawalpindi',
                'balance'    => 500,
                'country'    => 'Pakistan',
                'city'       => 'Rawalpindi',
                'postcode'   => '46000',
                'password'   => Hash::make('customer123'),
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
