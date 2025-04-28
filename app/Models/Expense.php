<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $fillable = ['store_id', 'expense_type', 'amount'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
