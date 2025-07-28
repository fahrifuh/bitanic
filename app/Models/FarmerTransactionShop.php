<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerTransactionShop extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'shipping_status',
        'delivery_receipt',
    ];

    /**
     * Get the shop that owns the FarmerTransactionShop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the farmer_transaction that owns the FarmerTransactionShop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function farmer_transaction(): BelongsTo
    {
        return $this->belongsTo(FarmerTransaction::class);
    }

    /**
     * Get all of the farmer_transaction_items for the FarmerTransactionShop
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farmer_transaction_items(): HasMany
    {
        return $this->hasMany(FarmerTransactionItem::class);
    }
}
