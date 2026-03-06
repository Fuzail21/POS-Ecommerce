<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // Each branch points to its OWN dedicated store warehouse (ids 5–9 from WarehouseSeeder).
        // Central warehouses (ids 1–4) are for purchasing only — stock must be transferred
        // from a central warehouse to the branch store before it appears in POS / Sale.
        DB::table('branches')->insert([
            ['name' => 'Lahore Main Branch',    'location' => 'Mall Road, Lahore',            'contact' => '042-35761234', 'warehouse_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Karachi Center Branch', 'location' => 'Tariq Road, Karachi',           'contact' => '021-34563210', 'warehouse_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Islamabad Branch',      'location' => 'Blue Area, Islamabad',          'contact' => '051-28874321', 'warehouse_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Peshawar Branch',       'location' => 'Saddar Bazar, Peshawar',        'contact' => '091-52563344', 'warehouse_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lahore Gulberg Branch', 'location' => 'MM Alam Road, Gulberg, Lahore', 'contact' => '042-35871100', 'warehouse_id' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ecommerce-store',       'location' => 'Online',                        'contact' => 'N/A',          'warehouse_id' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
