<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelInterpretation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'interpretation_id',
        'sangat_rendah',
        'rendah',
        'sedang',
        'tinggi',
        'sangat_tinggi',
    ];
}
