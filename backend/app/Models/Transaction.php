<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'amount',
        'currency',
        'provider', // payme | click
        'provider_order_id',
        'status', // pending, paid, failed
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
