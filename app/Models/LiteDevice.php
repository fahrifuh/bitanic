<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LiteDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lite_user_id',
        'number',
        'lite_series_id',
        'full_series',
        'version',
        'production_date',
        'purchase_date',
        'activate_date',
        'status',
        'image',
        'temperature',
        'water_temperature',
        'humidity',
        'min_tds',
        'max_tds',
        'min_ph',
        'max_ph',
        'current_tds',
        'current_ph',
        'last_updated_telemetri',
        'mode',
    ];

    /**
     * Get the lite_user that owns the LiteDevice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lite_user(): BelongsTo
    {
        return $this->belongsTo(LiteUser::class);
    }

    /**
     * Get the lite_series that owns the LiteDevice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lite_series(): BelongsTo
    {
        return $this->belongsTo(LiteSeries::class, 'lite_series_id');
    }

    /**
     * Get all of the pumps for the LiteDevice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pumps(): HasMany
    {
        return $this->hasMany(LiteDevicePump::class)->orderBy('number');
    }

    /**
     * Get the schedule associated with the LiteDevice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function schedule(): HasOne
    {
        return $this->hasOne(LiteDeviceSchedule::class)->where('is_finished', 0)->orderByDesc('created_at');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function($model){
            $model->number = LiteDevice::where('lite_series_id', $model->lite_series_id)->max('number') + 1;
            $model->full_series = $model->lite_series->prefix . "" . str_pad($model->number, 6, '0', STR_PAD_LEFT);
        });
    }
}
