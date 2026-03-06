<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailSettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mail_settings')->insert([
            [
                'mail_mailer'     => 'smtp',
                'mail_host'       => 'smtp.gmail.com',
                'mail_port'       => 587,
                'mail_username'   => 'info@alfalah-traders.pk',
                'mail_password'   => 'your-email-password',
                'mail_encryption' => 'tls',
                'sender_name'     => 'Al-Falah Traders',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
