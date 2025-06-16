<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use SoftDeletes;

    protected $table = 'sales_returns';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'branch_id',
        'return_date',
        'return_reason',
        'total_return_amount',
        'refund_amount',
        'payment_method',
        'created_by',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

