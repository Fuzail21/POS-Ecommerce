<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\PaymentTransaction;

class PaymentGatewayService
{
    /**
     * Build the JazzCash redirect payload for a sale.
     * Customer is redirected to JazzCash with these POST params.
     */
    public function buildJazzCashPayload(Sale $sale): array
    {
        $merchantId   = config('payment.jazzcash.merchant_id');
        $password     = config('payment.jazzcash.password');
        $integritySalt = config('payment.jazzcash.integrity_salt');
        $returnUrl    = config('payment.jazzcash.return_url');

        $txDateTime   = now()->format('Ymd His');
        $txRefNo      = 'T' . now()->format('YmdHis') . $sale->id;
        $amount       = (int) ($sale->final_amount * 100); // in paisas

        $hashString   = implode('&', [
            $integritySalt,
            $amount,
            '',       // BillReference
            '',       // CNIC
            '',       // CustomerEmailAddress
            '',       // CustomerMobileNo
            $merchantId,
            $password,
            $returnUrl,
            $txDateTime,
            $txRefNo,
            'PKR',
            'MWALLET',
        ]);

        $secureHash = hash_hmac('sha256', $hashString, $integritySalt);

        return [
            'pp_Version'            => '1.1',
            'pp_TxnType'            => 'MWALLET',
            'pp_Language'           => 'EN',
            'pp_MerchantID'         => $merchantId,
            'pp_Password'           => $password,
            'pp_TxnRefNo'           => $txRefNo,
            'pp_Amount'             => $amount,
            'pp_TxnCurrency'        => 'PKR',
            'pp_TxnDateTime'        => $txDateTime,
            'pp_BillReference'      => 'Order-' . $sale->invoice_number,
            'pp_Description'        => 'Payment for order ' . $sale->invoice_number,
            'pp_TxnExpiryDateTime'  => now()->addHour()->format('Ymd His'),
            'pp_ReturnURL'          => $returnUrl,
            'pp_SecureHash'         => $secureHash,
            'ppmpf_1'               => $sale->id,
        ];
    }

    /**
     * Process JazzCash/EasyPaisa callback and record the transaction.
     */
    public function handleCallback(array $data, string $gateway): PaymentTransaction
    {
        $saleId = $data['ppmpf_1'] ?? $data['sale_id'] ?? null;
        $sale   = Sale::findOrFail($saleId);

        $responseCode = $data['pp_ResponseCode'] ?? $data['responseCode'] ?? '999';
        $status       = ($responseCode === '000') ? 'success' : 'failed';

        // Idempotency: prevent duplicate processing if gateway retries the callback
        $transactionId = $data['pp_TxnRefNo'] ?? $data['transactionId'] ?? null;
        if ($transactionId) {
            $existing = PaymentTransaction::where('transaction_id', $transactionId)->first();
            if ($existing) {
                return $existing;
            }
        }

        $transaction = PaymentTransaction::create([
            'sale_id'              => $sale->id,
            'gateway'              => $gateway,
            'transaction_id'       => $data['pp_TxnRefNo'] ?? $data['transactionId'] ?? null,
            'pp_response_code'     => $responseCode,
            'pp_response_message'  => $data['pp_ResponseMessage'] ?? $data['responseMessage'] ?? null,
            'amount'               => $sale->final_amount,
            'status'               => $status,
            'gateway_payload'      => $data,
        ]);

        // If payment successful, mark sale as paid
        if ($status === 'success') {
            $sale->update([
                'paid_amount' => $sale->final_amount,
                'due_amount'  => 0,
                'status'      => 'confirmed',
            ]);
        }

        return $transaction;
    }

    /**
     * Build EasyPaisa redirect payload for a sale.
     */
    public function buildEasyPaisaPayload(Sale $sale): array
    {
        $storeId    = config('payment.easypaisa.store_id');
        $hashKey    = config('payment.easypaisa.hash_key');
        $returnUrl  = config('payment.easypaisa.return_url');
        $orderId    = 'EP-' . $sale->id . '-' . time();
        $amount     = number_format($sale->final_amount, 2, '.', '');
        $postBackUrl = $returnUrl;

        $hashData   = $storeId . $amount . $orderId . $postBackUrl . $hashKey;
        $hash       = strtoupper(hash('sha256', $hashData));

        return [
            'storeId'    => $storeId,
            'amount'     => $amount,
            'postBackURL' => $postBackUrl,
            'orderRefNum' => $orderId,
            'autoRedirect' => 1,
            'signature'  => $hash,
            'store_name' => config('app.name'),
            'ppmpf_1'    => $sale->id,
        ];
    }
}
