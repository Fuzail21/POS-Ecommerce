<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $table = 'cash_registers';
    protected $fillable = [
        'user_id', 'opened_at', 'closed_at',
        'opening_cash', 'closing_cash', 'total_sales',
        'total_expense', 'cash_difference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
