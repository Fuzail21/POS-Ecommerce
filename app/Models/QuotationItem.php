<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationItem extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'quotation_items';
    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_variant_id',
        'unit_price',
        'quantity',
        'discount_amount',
        'tax_amount',
        'subtotal',
    ];

    /**
     * Get the quotation that owns the quotation item.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

   // Relationship for simple products
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Relationship for product variants
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
