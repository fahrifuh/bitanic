<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HydroponicDeviceTelemetry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hydroponic_device_id',
        'sensors',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sensors' => 'object',
    ];

    /**
     * Get the hydroponicDevice that owns the HydroponicDeviceTelemetry
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hydroponicDevice(): BelongsTo
    {
        return $this->belongsTo(HydroponicDevice::class);
    }
}
