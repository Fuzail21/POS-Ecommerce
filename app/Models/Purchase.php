<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'vendor_id',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
