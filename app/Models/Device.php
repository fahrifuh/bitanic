<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'irrigation' => 'array',
        'vertigation' => 'array',
        'toren_pemupukan' => 'object',
    ];

    /**
     * Get all of the telemetri for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function telemetri(): HasMany
    {
        return $this->hasMany(Telemetri::class);
    }

    /**
     * Get the last_data_telemetri associated with the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function last_data_telemetri(): HasOne
    {
        return $this->hasOne(Telemetri::class)->orderBy('datetime', 'desc');
    }

    /**
     * Get all of the specification for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specification(): HasMany
    {
        return $this->hasMany(DeviceSpecification::class);
    }

    /**
     * Get the garden that owns the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    /**
     * Get all of the gardens for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gardens(): HasMany
    {
        return $this->hasMany(Garden::class);
    }

    /**
     * Get the farmer that owns the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    /**
     * Get the fertilization associated with the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fertilization(): HasOne
    {
        return $this->hasOne(Fertilization::class, 'device_id')->where('is_finished', 0);
    }

    /**
     * Get all of the finished_fertilization for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finished_fertilization(): HasMany
    {
        return $this->hasMany(Fertilization::class, 'device_id')->where('is_finished', 1);
    }

    /**
     * Get all of the fertilization_schedule for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fertilization_schedule(): HasMany
    {
        return $this->hasMany(FertilizationSchedule::class, 'device_id');
    }

    /**
     * Get all of the selenoids for the Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selenoids(): HasMany
    {
        return $this->hasMany(Selenoid::class)->orderBy('selenoid_id');
    }
}
