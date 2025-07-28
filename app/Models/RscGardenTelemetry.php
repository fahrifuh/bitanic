<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RscGardenTelemetry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rsc_garden_id',
        'latitude',
        'longitude',
        'samples->n',
        'samples->p',
        'samples->k',
        'samples->ec',
        'samples->ambient_temperature',
        'samples->soil_temperature',
        'samples->ambient_humidity',
        'samples->soil_moisture',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'samples' => 'object',
    ];

    /**
     * Get the rscGarden that owns the RscGardenTelemetry
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rscGarden(): BelongsTo
    {
        return $this->belongsTo(RscGarden::class);
    }
}
