<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Crop extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'moisture' => 'object',
    ];

    /**
     * Scope a query to only include sayur
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSayur($query)
    {
        return $query->where('type', 'sayur');
    }

    /**
     * Scope a query to only include buah
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuah($query)
    {
        return $query->where('type', 'buah');
    }

    /**
     * Get all of the garden for the Crop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function garden(): HasMany
    {
        return $this->hasMany(Garden::class);
    }

    /**
     * Get all of the harvest_produce for the Crop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function harvest_produce(): HasMany
    {
        return $this->hasMany(HarvestProduce::class);
    }
}
