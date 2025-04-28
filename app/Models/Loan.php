<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Vendor;

class Loan extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'user_type',
        'user_id',
        'amount',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
    
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'user_id');
    }
    
    
}
