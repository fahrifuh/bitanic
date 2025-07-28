<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Land extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'farmer_id',
        'name',
        'image',
        'latitude',
        'longitude',
        'altitude',
        'polygon',
        'area',
        'color',
        'address',
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
     * Get the farmer that owns the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    /**
     * Get all of the gardens for the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gardens(): HasMany
    {
        return $this->hasMany(Garden::class);
    }

    /**
     * Get the use_garden associated with the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function use_garden(): HasOne
    {
        return $this->hasOne(Garden::class)->whereIn('harvest_status', [0,1,2]);
    }

    /**
     * Get the selenoid associated with the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function selenoid(): HasOne
    {
        return $this->hasOne(Selenoid::class);
    }

    /**
     * Get the rsc_telemetri associated with the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rsc_telemetri(): HasOne
    {
        return $this->hasOne(RscTelemetri::class);
    }

    /**
     * Get all of the rsc_telemetries for the Land
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rsc_telemetries(): HasMany
    {
        return $this->hasMany(RscTelemetri::class);
    }
}
