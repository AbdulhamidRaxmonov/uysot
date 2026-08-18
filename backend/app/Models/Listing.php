<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'currency',
        'type', // sale/rent/daily
        'address',
        'city',
        'photos', // json array of urls
        'is_vip',
        'owner_id'
    ];

    protected $casts = [
        'photos' => 'array',
        'is_vip' => 'boolean'
    ];
}
