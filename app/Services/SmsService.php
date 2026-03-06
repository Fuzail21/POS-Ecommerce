<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS via the configured Pakistani SMS gateway.
     * Configure in .env:
     *   SMS_GATEWAY=smspk        (smspk | ecosms | zong)
     *   SMS_API_KEY=your_key
     *   SMS_SENDER_ID=YourBrand
     */
    public function send(string $phone, string $message): bool
    {
        if (!config('services.sms.key')) {
            Log::info('SMS not sent (no API key configured). To: ' . $phone . ' | ' . $message);
            return false;
        }

        $phone = $this->normalizePhone($phone);

        try {
            $gateway = config('services.sms.gateway', 'smspk');

            return match ($gateway) {
                'smspk'   => $this->sendViaSMSPK($phone, $message),
                'ecosms'  => $this->sendViaEcoSMS($phone, $message),
                default   => $this->sendViaSMSPK($phone, $message),
            };
        } catch (\Exception $e) {
            Log::warning('SMS send failed: ' . $e->getMessage());
            return false;
        }
    }

    private function sendViaSMSPK(string $phone, string $message): bool
    {
        $response = Http::get('https://api.smspk.net/sms/send', [
            'api_key'   => config('services.sms.key'),
            'to'        => $phone,
            'message'   => $message,
            'sender_id' => config('services.sms.sender_id', 'INFO'),
        ]);

        return $response->successful();
    }

    private function sendViaEcoSMS(string $phone, string $message): bool
    {
        $response = Http::post('https://www.ecosms.pk/api/sendsms', [
            'api_key'   => config('services.sms.key'),
            'sender'    => config('services.sms.sender_id', 'INFO'),
            'number'    => $phone,
            'message'   => $message,
        ]);

        return $response->successful();
    }

    /**
     * Normalize phone to international Pakistani format: 923XXXXXXXXX
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '92'))  return $phone;
        if (str_starts_with($phone, '0'))   return '92' . substr($phone, 1);
        if (str_starts_with($phone, '3'))   return '92' . $phone;

        return $phone;
    }

    // ── Convenience methods ──────────────────────────────────────────────────

    public function sendOrderPlaced(string $phone, string $invoiceNo, float $amount): bool
    {
        $symbol  = \App\Models\Setting::first()?->currency_symbol ?? 'Rs';
        $message = "Thank you for your order! Invoice: {$invoiceNo} | Amount: {$symbol} {$amount}. We'll notify you once it's confirmed.";
        return $this->send($phone, $message);
    }

    public function sendOrderStatusUpdated(string $phone, string $invoiceNo, string $status): bool
    {
        $message = "Your order {$invoiceNo} status has been updated to: " . strtoupper($status) . ". Visit the website to track your order.";
        return $this->send($phone, $message);
    }

    public function sendPaymentReceived(string $phone, string $invoiceNo, float $amount): bool
    {
        $symbol  = \App\Models\Setting::first()?->currency_symbol ?? 'Rs';
        $message = "Payment of {$symbol} {$amount} received for order {$invoiceNo}. Thank you!";
        return $this->send($phone, $message);
    }
}
