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
            ['name' => 'Milligram', 'symbol' => 'mg'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Ton', 'symbol' => 't'],
            ['name' => 'Ounce', 'symbol' => 'oz'],
            ['name' => 'Pound', 'symbol' => 'lb'],

            // Volume
            ['name' => 'Millilitre', 'symbol' => 'ml'],
            ['name' => 'Litre', 'symbol' => 'L'],
            ['name' => 'Cubic Centimeter', 'symbol' => 'cm³'],
            ['name' => 'Cubic Meter', 'symbol' => 'm³'],
            ['name' => 'Gallon', 'symbol' => 'gal'],
            ['name' => 'Pint', 'symbol' => 'pt'],
            ['name' => 'Quart', 'symbol' => 'qt'],
            ['name' => 'Fluid Ounce', 'symbol' => 'fl oz'],

            // Length
            ['name' => 'Millimeter', 'symbol' => 'mm'],
            ['name' => 'Centimeter', 'symbol' => 'cm'],
            ['name' => 'Meter', 'symbol' => 'm'],
            ['name' => 'Kilometer', 'symbol' => 'km'],
            ['name' => 'Inch', 'symbol' => 'in'],
            ['name' => 'Foot', 'symbol' => 'ft'],
            ['name' => 'Yard', 'symbol' => 'yd'],
            ['name' => 'Mile', 'symbol' => 'mi'],

            // Area
            ['name' => 'Square Meter', 'symbol' => 'm²'],
            ['name' => 'Square Kilometer', 'symbol' => 'km²'],
            ['name' => 'Square Foot', 'symbol' => 'ft²'],
            ['name' => 'Acre', 'symbol' => 'ac'],
            ['name' => 'Hectare', 'symbol' => 'ha'],

            // Quantity
            ['name' => 'Piece', 'symbol' => 'pc'],
            ['name' => 'Dozen', 'symbol' => 'doz'],
            ['name' => 'Pack', 'symbol' => 'pack'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Roll', 'symbol' => 'roll'],
            ['name' => 'Set', 'symbol' => 'set'],
            ['name' => 'Bottle', 'symbol' => 'btl'],
            ['name' => 'Can', 'symbol' => 'can'],
            ['name' => 'Carton', 'symbol' => 'ctn'],
        ]);
    }
}
