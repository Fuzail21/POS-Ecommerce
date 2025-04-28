<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profit extends Model
{
    use HasFactory;

    protected $table = 'profits';
    protected $fillable = ['store_id', 'total_income', 'total_expense'];

    
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}
