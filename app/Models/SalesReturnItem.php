<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductVariant; // <--- Make sure this line is present
use App\Models\Product;
use App\Models\Unit;
use App\Models\Sale;
use App\Models\SalesReturn;


class SalesReturnItem extends Model
{
    use SoftDeletes;

    protected $table = 'sales_return_items';

    protected $fillable = [
        'sales_return_id',
        'sale_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_id',
        'quantity_in_base_unit',
        'unit_price',
        'discount',
        'tax',
        'total_price',
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        // THIS IS THE LINE TO CORRECT:
        return $this->belongsTo(ProductVariant::class, 'variant_id'); // Use ProductVariant::class
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}