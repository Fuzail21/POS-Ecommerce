<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    | Supported: "jazzcash", "easypaisa"
    */
    'default' => env('PAYMENT_GATEWAY', 'jazzcash'),

    'jazzcash' => [
        'merchant_id'   => env('JAZZCASH_MERCHANT_ID', ''),
        'password'      => env('JAZZCASH_PASSWORD', ''),
        'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT', ''),
        'return_url'    => env('JAZZCASH_RETURN_URL', ''),
        // Sandbox vs Production
        'endpoint'      => env('JAZZCASH_SANDBOX', true)
            ? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'
            : 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/',
    ],

    'easypaisa' => [
        'store_id'      => env('EASYPAISA_STORE_ID', ''),
        'hash_key'      => env('EASYPAISA_HASH_KEY', ''),
        'account_num'   => env('EASYPAISA_ACCOUNT_NUM', ''),
        'return_url'    => env('EASYPAISA_RETURN_URL', ''),
        'endpoint'      => env('EASYPAISA_SANDBOX', true)
            ? 'https://easypaystg.easypaisa.com.pk/easypay/'
            : 'https://easypay.easypaisa.com.pk/easypay/',
    ],

];
