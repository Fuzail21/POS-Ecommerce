<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_items';
    protected $fillable = [
        'purchase_id', 'product_id', 'variant_id',
        'batch_no', 'expiry_date', 'quantity_in_base_unit',
        'unit_cost', 'total_cost', 'quantity'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

