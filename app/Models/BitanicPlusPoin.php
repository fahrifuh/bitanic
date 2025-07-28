<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BitanicPlusPoin extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the BitanicPlusPoin
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the poin_history for the BitanicPlusPoin
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poin_history(): HasMany
    {
        return $this->hasMany(BitanicPlusPoinHistory::class, 'bitanic_plus_poin_id');
    }
}
