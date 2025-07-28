<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class FarmerTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'status',
    ];

    /**
     * Get all of the farmer_transaction_shops for the FarmerTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farmer_transaction_shops(): HasMany
    {
        return $this->hasMany(FarmerTransactionShop::class);
    }

    /**
     * Get the user that owns the FarmerTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the farmer_transaction_items for the FarmerTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function farmer_transaction_items(): HasManyThrough
    {
        return $this->hasManyThrough(FarmerTransactionItem::class, FarmerTransactionShop::class);
    }
}
