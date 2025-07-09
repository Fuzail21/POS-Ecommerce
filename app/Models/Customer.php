<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this line
use Illuminate\Foundation\Auth\User as Authenticatable; // Change Model to Authenticatable
use Illuminate\Notifications\Notifiable; // Add this line

class Customer extends Authenticatable // Extend Authenticatable instead of Model
{
    use HasFactory, Notifiable; // Add HasFactory and Notifiable traits

    protected $table = 'customers';

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'balance', 'card_id', 'password' // Add 'password' to fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // Add remember_token if you're using "remember me" functionality
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // If you plan to implement email verification
        'password' => 'hashed', // Laravel 10+ automatically hashes passwords if cast to 'hashed'
    ];


    public function payments()
    {
        return $this->morphMany(Payment::class, 'entity', 'entity_type', 'entity_id');
    }
}