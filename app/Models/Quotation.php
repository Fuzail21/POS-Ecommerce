<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'quotations';
    protected $fillable = [
        'quotation_number',
        'customer_id',
        'branch_id',
        'quotation_date',
        'order_tax_percentage',
        'discount_percentage',
        'shipping_cost',
        'grand_total',
        'status',
        'note',
    ];

    /**
     * Get the customer that owns the quotation.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class); // Assuming you have a Customer model
    }

    /**
     * Get the warehouse that the quotation belongs to.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class); // Assuming you have a Warehouse model
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class); // Assuming you have a Warehouse model
    }

    /**
     * Get the quotation items for the quotation.
     */
    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
