<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InventoryStock;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_variants';
    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'barcode',
        'sale_price',
        'product_img',
        'low_stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function inventoryStock()
    {
        return $this->hasOne(InventoryStock::class, 'variant_id');
    }
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }
    public function displayUnit()
    {
        return $this->belongsTo(Unit::class, 'display_unit_id');
    }

    public function inventoryStocks()
    {
        // Corrected line: Use a comma (,) to separate the arguments
        return $this->hasMany(InventoryStock::class, 'variant_id');
    }
}

