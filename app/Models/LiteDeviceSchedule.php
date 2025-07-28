<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiteDeviceSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lite_device_id',
        'crop_id',
        'crop_name',
        'weeks',
        'days',
        'setontimes',
        'end_datetime',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'days' => 'array',
        'setontimes' => 'array',
    ];

    /**
     * Get the lite_device that owns the LiteDeviceSchedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lite_device(): BelongsTo
    {
        return $this->belongsTo(LiteDevice::class);
    }

    /**
     * Get the crop that owns the LiteDeviceSchedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
