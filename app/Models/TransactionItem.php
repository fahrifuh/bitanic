<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'bitanic_product_id',
        'province_id',
        'city_id',
        'name',
        'price',
        'weight',
        'shipping_price',
        'quantity',
        'total',
        'discount',
    ];

    /**
     * Get the bitanic_product that owns the TransactionItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bitanic_product(): BelongsTo
    {
        return $this->belongsTo(BitanicProduct::class);
    }
}
