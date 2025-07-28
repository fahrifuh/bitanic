<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Garden extends Model
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
        'polygon' => 'array',
    ];

    /**
     * Scope a query to only include planting
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlanting($query)
    {
        return $query->where('harvest_status', 1);
    }

    /**
     * Scope a query to only include maintenance period
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaintenancePeriod($query)
    {
        return $query->where('harvest_status', 2);
    }

    /**
     * Scope a query to only include harvest period
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHarvestPeriod($query)
    {
        return $query->where('harvest_status', 3);
    }

    /**
     * Get the land that owns the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Get the crop that owns the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the device associated with the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function device(): HasOne
    // {
    //     return $this->hasOne(Device::class);
    // }

    /**
     * Get the device that owns the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the pest that owns the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pest(): BelongsTo
    {
        return $this->belongsTo(Pest::class);
    }

    /**
     * Get the unfinished_fertilization associated with the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unfinished_fertilization(): HasOne
    {
        return $this->hasOne(Fertilization::class)->where('is_finished', 0);
    }

    /**
     * Get all of the finished_fertilization for the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finished_fertilization(): HasMany
    {
        return $this->hasMany(Fertilization::class)->where('is_finished', 1);
    }

    /**
     * Get all of the fertilization for the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fertilizations(): HasMany
    {
        return $this->hasMany(Fertilization::class);
    }

    /**
     * Get the active_garden associated with the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function active_garden(): HasOne
    {
        return $this->hasOne(ActiveGarden::class)->whereNull('finished_date');
    }

    /**
     * Get all of the active_gardens for the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function active_gardens(): HasMany
    {
        return $this->hasMany(ActiveGarden::class);
    }

    /**
     * Get all of the invected for the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invected(): HasMany
    {
        return $this->hasMany(InvectedGarden::class);
    }

    /**
     * Get the harvest_produce associated with the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function harvest_produce(): HasOne
    {
        return $this->hasOne(HarvestProduce::class);
    }

    /**
     * Get the currentCommodity associated with the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentCommodity(): HasOne
    {
        return $this->hasOne(Commodity::class)->where('is_finished', 0);
    }

    /**
     * Get all of the historyCommodities for the Garden
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finishedCommodities(): HasMany
    {
        return $this->hasMany(Commodity::class)->where('is_finished', 1)->orderByDesc('created_at');
    }
}
