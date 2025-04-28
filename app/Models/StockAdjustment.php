<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'adjustment_type',
        'quantity',
        'reason',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
