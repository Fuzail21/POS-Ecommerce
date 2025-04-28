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
}
