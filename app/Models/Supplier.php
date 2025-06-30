<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'balance',
    ];

    public function payments()
    {
        return $this->morphMany(Payment::class, 'entity', 'entity_type', 'entity_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier');
    }
}

