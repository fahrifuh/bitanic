<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StikTelemetri extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_perangkat',
        'id_pengukuran',
        'temperature',
        'moisture',
        'rh',
        't',
        'n',
        'p',
        'k',
        'type',
        'polygon',
        'longitude',
        'latitude',
        'co2',
        'no2',
        'n2o',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'polygon' => 'array',
    ];

    /**
     * Get the user that owns the StikTelemetri
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
