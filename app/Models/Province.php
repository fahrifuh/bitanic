<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at', 'id'];

    /**
     * Get all of the city for the Province
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function city(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get all of the district for the Province
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function districts(): HasManyThrough
    {
        return $this->hasManyThrough(
            District::class, 
            City::class,
            'province_id',
            'city_id',
            'id',
            'id'
        );
    }
}
