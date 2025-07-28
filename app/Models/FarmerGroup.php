<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at','updated_at'];

    /**
     * Get the subdistrict that owns the FarmerGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subdistrict(): BelongsTo
    {
        return $this->belongsTo(Subdistrict::class, 'subdis_id');
    }

    /**
     * Get all of the farmers for the FarmerGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class, 'group_id');
    }
}
