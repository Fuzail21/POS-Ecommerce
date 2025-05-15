<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units'; 

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
