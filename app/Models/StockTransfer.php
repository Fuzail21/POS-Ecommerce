<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $table = 'stock_transfers';

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'product_id',
        'variant_id',
        'quantity',
        'transfer_reference',
        'notes',
        'created_by',
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
