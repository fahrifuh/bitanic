<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BitanicProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'picture',
        'name',
        'price',
        'description',
        'version',
        'type',
        'category',
        'is_listed',
        'weight',
        'discount',
    ];

    public function farmers(): BelongsToMany
    {
        return $this->belongsToMany(Farmer::class, 'bitanic_product_farmer');
    }
}
