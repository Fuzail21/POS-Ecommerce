<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StockLedger extends Model
{
    use SoftDeletes;

    protected $table = 'stock_ledgers';

    protected $fillable = [
        'product_id',
        'variant_id',
        'warehouse_id',
        'ref_type',
        'ref_id',
        'quantity_change_in_base_unit',
        'unit_cost',
        'direction',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

