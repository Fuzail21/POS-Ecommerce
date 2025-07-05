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
        'actual_price',
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

    public function getDiscountedPriceAttribute()
    {
        $price = $this->actual_price;
        $today = now()->toDateString();
    
        $rules = \App\Models\DiscountRule::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();
    
        foreach ($rules as $rule) {
            $ids = json_decode($rule->target_ids, true);
        
            // Match by product
            if ($rule->type === 'product' && in_array($this->product_id, $ids)) {
                return $this->applyPercentageDiscount($price, $rule->discount);
            }
        
            // Match by category (via parent product)
            if (
                $rule->type === 'category' &&
                $this->product && // check relation exists
                in_array($this->product->category_id, $ids)
            ) {
                return $this->applyPercentageDiscount($price, $rule->discount);
            }
        }
    
        return $price;
    }
    
    private function applyPercentageDiscount($price, $discount)
    {
        return round($price - ($price * $discount / 100), 2);
    }

}

