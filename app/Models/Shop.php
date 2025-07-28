<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'farmer_id',
        'name',
        'picture',
        'ktp',
        'is_ktp_validated',
        'address',
        'latitude',
        'longitude',
        'province_id',
        'city_id',
        'bank_account',
        'bank_type',
    ];

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
     * Get the farmer that owns the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    /**
     * Get all of the products for the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all of the farmer_transaction_shops for the Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farmer_transaction_shops(): HasMany
    {
        return $this->hasMany(FarmerTransactionShop::class);
    }
}
