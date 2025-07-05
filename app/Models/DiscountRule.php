<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    protected $table = 'discount_rules';
    protected $fillable = [
        'name', // Added 'name' to the fillable attributes
        'type',
        'target_ids',
        'discount',
        'start_date',
        'end_date',
    ];

}
