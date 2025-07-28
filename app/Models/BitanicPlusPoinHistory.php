<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitanicPlusPoinHistory extends Model
{
    use HasFactory;

    /**
     * Get the bitanic_plus_poin that owns the BitanicPlusPoinHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bitanic_plus_poin(): BelongsTo
    {
        return $this->belongsTo(BitanicPlusPoin::class);
    }
}
