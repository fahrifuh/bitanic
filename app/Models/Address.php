<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'postal_code',
        'latitude',
        'longitude',
        'detail',
        'user_id',
        'recipient_name',
        'recipient_phone_number',
        'province_id',
        'city_id',
    ];
}
