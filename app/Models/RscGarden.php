<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RscGarden extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'garden_id',
    ];

    /**
     * Get the device that owns the RscGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the garden that owns the RscGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    /**
     * Get all of the rscGardenTelemetries for the RscGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rscGardenTelemetries(): HasMany
    {
        return $this->hasMany(RscGardenTelemetry::class);
    }
}
