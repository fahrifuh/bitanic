<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvectedGarden extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the pest that owns the InvectedGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pest(): BelongsTo
    {
        return $this->belongsTo(Pest::class);
    }

    /**
     * Get the garden that owns the InvectedGarden
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }
}
