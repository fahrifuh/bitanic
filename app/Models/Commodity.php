<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commodity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'garden_id',
        'crop_id',
        'planting_dates',
        'estimated_harvest',
        'total',
        'is_finished',
        'value',
        'unit',
        'harvested',
        'note',
    ];

    /**
     * Get the crop that owns the Commodity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
