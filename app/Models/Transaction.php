<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'midtrans_token',
        'status',
        'total',
        'platform_fees',
        'discount',
        'user_id',
        'address_id',
        'province_id',
        'city_id',
        'user_recipient_name',
        'user_recipient_phone_number',
        'user_address',
        'courier',
        'type',
        'bank_name',
        'bank_code',
        'bank_fees',
        'shipping_status',
        'delivery_receipt',
    ];

    /**
     * Get the transaction_item associated with the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transaction_item(): HasOne
    {
        return $this->hasOne(TransactionItem::class);
    }

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the address that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
