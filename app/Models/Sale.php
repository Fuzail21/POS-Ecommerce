<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    protected $table = 'sales';

    protected $fillable = [
        'customer_id', 'user_id', 'total_amount', 'discount', 'tax', 'payment_status'
    ];

    public function items()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(SalesPayment::class);
    }

    public function discountTax()
    {
        return $this->hasOne(SalesDiscountTax::class);
    }
    
    public function getFinalTotalAttribute()
    {
        $discount = $this->discountTax->discount ?? 0;
        $tax = $this->discountTax->tax ?? 0;
        $discountAmount = ($this->total_amount * $discount) / 100;
        $taxableAmount = $this->total_amount - $discountAmount;
        $taxAmount = ($taxableAmount * $tax) / 100;
    
        return round($taxableAmount + $taxAmount, 2);
    }
    

}
