<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesDiscountTax extends Model
{
    use HasFactory;

    protected $table = 'sales_discounts_taxes';

    protected $fillable = [
        'sale_id',
        'discount',
        'tax',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
