<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';
    
    protected $fillable = [
        'entity_type',
        'entity_id',
        'transaction_type',
        'amount',
        'payment_method',
        'ref_type',
        'ref_id',
        'note',
        'created_by',
    ];

    /**
     * Get the related entity (customer or supplier)
     */
    public function entity()
    {
        return $this->morphTo(null, 'entity_type', 'entity_id');
    }

    public function reference()
    {
        return $this->morphTo(null, 'ref_type', 'ref_id');
    }

    /**
     * Optional: get the user who created the payment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

