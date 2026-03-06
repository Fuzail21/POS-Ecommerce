<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'business_name'   => 'Al-Falah Traders',
                'logo_path'       => null,
                'currency_symbol' => 'Rs',
                'currency_code'   => 'PKR',
                'timezone'        => 'Asia/Karachi',
                'primary_color'   => '#1a6b3c',
                'secondary_color' => '#f5a623',
                'default_email'   => 'info@alfalah-traders.pk',
                'company_phone'   => '042-35761234',
                'footer'          => 'Thank you for shopping with us! Visit again.',
                'country'         => 'Pakistan',
                'state'           => 'Punjab',
                'city'            => 'Lahore',
                'postal_code'     => '54000',
                'address'         => 'Shop No. 5, Main Market, Mall Road, Lahore',
                'developed_by'    => 'TechSoft Pakistan',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
