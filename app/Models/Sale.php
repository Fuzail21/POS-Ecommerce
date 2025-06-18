<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment;
use App\Models\SaleItem;

class Sale extends Model
{
    use SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'customer_id',
        'branch_id',
        'invoice_number',
        'sale_date',
        'total_amount',
        'discount_amount',
        'tax_amount',
        'final_amount',
        'shipping',
        'paid_amount',
        'due_amount',
        'payment_method',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'reference', 'ref_type', 'ref_id');
    }

    public function getDueAttribute()
    {
        return $this->total - $this->payments()->sum('amount');
    }
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class, 'sale_id'); // Ensure 'sale_id' is the foreign key in sales_returns table
    }

}
