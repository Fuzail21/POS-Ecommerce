<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'amount_paid',
        'payment_method',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
