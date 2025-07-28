<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HydroponicDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'series',
        'version',
        'user_id',
        'activation_date',
        'production_date',
        'purchase_date',
        'picture',
        'is_auto',
        'thresholds',
        'pumps',
        'note',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'thresholds' => 'object',
        'pumps' => 'object',
    ];

    /**
     * Get the hydroponicUser that owns the HydroponicDevice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hydroponicUser(): BelongsTo
    {
        return $this->belongsTo(HydroponicUser::class, 'user_id');
    }

    /**
     * Get the latestTelemetry associated with the HydroponicDevice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestTelemetry(): HasOne
    {
        return $this->hasOne(HydroponicDeviceTelemetry::class)->ofMany('created_at', 'max');
    }
}
