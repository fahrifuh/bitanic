<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Interpretation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nama'];

    /**
     * Get the levelInterpretation associated with the Interpretation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function level_interpretation(): HasOne
    {
        return $this->hasOne(LevelInterpretation::class,);
    }
}
