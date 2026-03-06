<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'sale_id',
        'gateway',
        'transaction_id',
        'pp_response_code',
        'pp_response_message',
        'amount',
        'status',
        'gateway_payload',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'amount'          => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
