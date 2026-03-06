<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class MailSetting extends Model
{
    protected $table = 'mail_settings';

    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'sender_name',
    ];

    /**
     * Encrypt on save, decrypt on read.
     * Falls back gracefully if the stored value is still plaintext (legacy).
     */
    protected function mailPassword(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return $value;
                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException $e) {
                    // Stored as plaintext (pre-encryption) — return as-is
                    return $value;
                }
            },
            set: fn ($value) => $value ? Crypt::encryptString($value) : $value,
        );
    }
}
