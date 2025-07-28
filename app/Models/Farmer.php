<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Farmer extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['ktp'];

    /**
     * Get the is ktp uploaded
     *
     * @return string
     */
    public function getIsKtpUploadedAttribute()
    {
        return $this->ktp !== null;
    }

    protected $appends = ['is_ktp_uploaded'];

    /**
     * Get the user that owns the Farmer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the garden for the Farmer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function garden(): HasMany
    {
        return $this->hasMany(Garden::class);
    }

    /**
     * Get all of the telemetri for the Farmer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function telemetri(): HasMany
    {
        return $this->hasMany(Telemetri::class);
    }

    /**
     * Get the group that owns the Farmer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(FarmerGroup::class, 'group_id');
    }

    /**
     * Get the shop associated with the Farmer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function bitanicProducts(): BelongsToMany
    {
        return $this->belongsToMany(BitanicProduct::class, 'bitanic_product_farmer');
    }
}
