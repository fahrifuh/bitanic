<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class District extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];

    /**
     * Get the city that owns the District
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get all of the subdistrict for the District
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subdistrict(): HasMany
    {
        return $this->hasMany(Subdistrict::class, 'dis_id');
    }

    /**
     * Get all of the farmer_groups for the District
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function farmer_groups(): HasManyThrough
    {
        return $this->hasManyThrough(
            FarmerGroup::class, 
            Subdistrict::class,
            'dis_id',
            'subdis_id',
            'id',
            'id'
        );
    }
}
