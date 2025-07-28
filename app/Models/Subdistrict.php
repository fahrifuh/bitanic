<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subdistrict extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];

    /**
     * Get the district that owns the Subdistrict
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'dis_id');
    }

    /**
     * Get all of the farmer_groups for the Subdistrict
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farmer_groups(): HasMany
    {
        return $this->hasMany(FarmerGroup::class, 'subdis_id');
    }
}
