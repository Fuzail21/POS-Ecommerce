<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// add these two:
use App\Models\Customer;
use App\Models\Vendor;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    // include your foreign key columns here so you can mass-assign them if you ever need to
    protected $fillable = [
        'user_type',
        'user_id',
        'amount',
        'method',
        'status',
        'payment_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'user_id');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

}

