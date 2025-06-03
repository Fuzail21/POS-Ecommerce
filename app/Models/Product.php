<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'category_id',
        'base_unit_id',
        'default_display_unit_id',
        'has_variants',
        'sku',
        'barcode',
        'brand',
        'track_expiry',
        'tax_rate',
        'sale_price',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function baseUnit() {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function displayUnit() {
        return $this->belongsTo(Unit::class, 'default_display_unit_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventoryStock()
    {
        return $this->hasOne(InventoryStock::class, 'product_id');
    }


}

