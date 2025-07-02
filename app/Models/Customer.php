<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'balance', 'card_id'
    ];

    public function payments()
    {
        return $this->morphMany(Payment::class, 'entity', 'entity_type', 'entity_id');
    }

}

