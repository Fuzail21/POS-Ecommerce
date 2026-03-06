<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            // Mass
            ['name' => 'Milligram'],
            ['name' => 'Gram'],
            ['name' => 'Kilogram'],
            ['name' => 'Ton'],
            ['name' => 'Ounce'],
            ['name' => 'Pound'],

            // Volume
            ['name' => 'Millilitre'],
            ['name' => 'Litre'],
            ['name' => 'Cubic Centimeter'],
            ['name' => 'Cubic Meter'],
            ['name' => 'Gallon'],
            ['name' => 'Pint'],
            ['name' => 'Quart'],
            ['name' => 'Fluid Ounce'],

            // Length
            ['name' => 'Millimeter'],
            ['name' => 'Centimeter'],
            ['name' => 'Meter'],
            ['name' => 'Kilometer'],
            ['name' => 'Inch'],
            ['name' => 'Foot'],
            ['name' => 'Yard'],
            ['name' => 'Mile'],

            // Area
            ['name' => 'Square Meter'],
            ['name' => 'Square Kilometer'],
            ['name' => 'Square Foot'],
            ['name' => 'Acre'],
            ['name' => 'Hectare'],

            // Quantity
            ['name' => 'Piece'],
            ['name' => 'Dozen'],
            ['name' => 'Pack'],
            ['name' => 'Box'],
            ['name' => 'Roll'],
            ['name' => 'Set'],
            ['name' => 'Bottle'],
            ['name' => 'Can'],
            ['name' => 'Carton'],
        ]);
    }
}
