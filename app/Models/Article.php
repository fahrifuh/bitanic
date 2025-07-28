<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['status_like'];
    public static $withoutAppends = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['article_like'];

    protected function getArrayableAppends()
    {
        if(self::$withoutAppends){
            return [];
        }
        return parent::getArrayableAppends();
    }

    public function getStatusLikeAttribute(){
        return collect($this->article_like)->where('id', auth()->user()->id)->first() ? 1 : 0;
    }

    /**
     * The article_view that belong to the Article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function article_view(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_view', 'article_id', 'user_id');
    }

    /**
     * The article_like that belong to the Article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function article_like(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_like', 'article_id', 'user_id');
    }
}
