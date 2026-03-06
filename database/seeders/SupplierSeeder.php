<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'name'           => 'Al-Harmain Traders',
                'contact_person' => 'Muhammad Arsalan',
                'phone'          => '0300-4521234',
                'email'          => 'alharamain@traders.pk',
                'address'        => 'Hall Road, Lahore',
                'balance'        => 0,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Karachi Electronics Co.',
                'contact_person' => 'Salman Ahmed',
                'phone'          => '0333-2189900',
                'email'          => 'karachi.elec@gmail.com',
                'address'        => 'Saddar, Karachi',
                'balance'        => 15000,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Punjab Foods & Grains',
                'contact_person' => 'Riaz Chaudhry',
                'phone'          => '042-37645500',
                'email'          => 'punjabfoods@pk.com',
                'address'        => 'Shahdara, Lahore',
                'balance'        => 0,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'Modern Garments',
                'contact_person' => 'Imran Sheikh',
                'phone'          => '0321-9087654',
                'email'          => 'moderngarm@gmail.com',
                'address'        => 'Faisalabad Textile Market',
                'balance'        => 8500,
                'created_at'     => now(), 'updated_at' => now(),
            ],
            [
                'name'           => 'HiTech Mobile Distributors',
                'contact_person' => 'Nadeem Ahmed',
                'phone'          => '0345-7654321',
                'email'          => 'hightechmobile@pk.com',
                'address'        => 'Hafeez Center, Lahore',
                'balance'        => 25000,
                'created_at'     => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
