<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\MailSetting;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (\Schema::hasTable('mail_settings')) {
                $mail = \App\Models\MailSetting::first();

                if ($mail) {
                    Config::set('mail.default', $mail->mail_mailer); // ✅ this is mandatory
                    Config::set('mail.mailers.smtp.transport', $mail->mail_mailer); // optional; smtp
                    Config::set('mail.mailers.smtp.host', $mail->mail_host);
                    Config::set('mail.mailers.smtp.port', $mail->mail_port);
                    Config::set('mail.mailers.smtp.username', $mail->mail_username);
                    Config::set('mail.mailers.smtp.password', $mail->mail_password);
                    Config::set('mail.mailers.smtp.encryption', $mail->mail_encryption);
                    Config::set('mail.from.address', $mail->mail_username); // ✅ assuming sender email
                    Config::set('mail.from.name', $mail->sender_name ?? 'Laravel');
                }
            }
        } catch (\Exception $e) {
            // Table may be missing or corrupted — skip mail config until migrations run
        }
    }

}
