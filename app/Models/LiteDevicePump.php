<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiteDevicePump extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lite_device_id',
        'min_tds',
        'max_tds',
        'min_ph',
        'max_ph',
        'number',
        'name',
        'is_active',
        'status',
    ];
}
