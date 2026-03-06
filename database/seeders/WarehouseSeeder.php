<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // Central / purchasing warehouses (stock lands here on purchase)
        DB::table('warehouses')->insert([
            ['name' => 'Lahore Central Warehouse',    'location' => 'Kot Lakhpat, Lahore',                   'capacity' => '50000', 'capacity_unit' => 'units', 'used_capacity' => 12000, 'created_at' => now(), 'updated_at' => now()], // id 1
            ['name' => 'Karachi Storage Warehouse',   'location' => 'SITE Area, Karachi',                    'capacity' => '80000', 'capacity_unit' => 'units', 'used_capacity' => 25000, 'created_at' => now(), 'updated_at' => now()], // id 2
            ['name' => 'Islamabad Main Warehouse',    'location' => 'I-9 Industrial Area, Islamabad',        'capacity' => '30000', 'capacity_unit' => 'units', 'used_capacity' =>  8000, 'created_at' => now(), 'updated_at' => now()], // id 3
            ['name' => 'Peshawar Central Warehouse',  'location' => 'Hayatabad Industrial Estate, Peshawar', 'capacity' => '20000', 'capacity_unit' => 'units', 'used_capacity' =>  5000, 'created_at' => now(), 'updated_at' => now()], // id 4
        ]);

        // Branch-level store warehouses (stock arrives here via transfer, used for sales/POS)
        DB::table('warehouses')->insert([
            ['name' => 'Lahore Main Branch Store',    'location' => 'Mall Road, Lahore',            'capacity' => '5000', 'capacity_unit' => 'units', 'used_capacity' => 0, 'created_at' => now(), 'updated_at' => now()], // id 5
            ['name' => 'Karachi Center Branch Store', 'location' => 'Tariq Road, Karachi',           'capacity' => '5000', 'capacity_unit' => 'units', 'used_capacity' => 0, 'created_at' => now(), 'updated_at' => now()], // id 6
            ['name' => 'Islamabad Branch Store',      'location' => 'Blue Area, Islamabad',          'capacity' => '3000', 'capacity_unit' => 'units', 'used_capacity' => 0, 'created_at' => now(), 'updated_at' => now()], // id 7
            ['name' => 'Peshawar Branch Store',       'location' => 'Saddar Bazar, Peshawar',        'capacity' => '3000', 'capacity_unit' => 'units', 'used_capacity' => 0, 'created_at' => now(), 'updated_at' => now()], // id 8
            ['name' => 'Lahore Gulberg Branch Store', 'location' => 'MM Alam Road, Gulberg, Lahore', 'capacity' => '4000', 'capacity_unit' => 'units', 'used_capacity' => 0, 'created_at' => now(), 'updated_at' => now()], // id 9
            ['name' => 'Ecommerce Store Warehouse',   'location' => 'Online / Virtual',               'capacity' => '999999', 'capacity_unit' => 'units', 'used_capacity' => 0, 'created_at' => now(), 'updated_at' => now()], // id 10
        ]);
    }
}
