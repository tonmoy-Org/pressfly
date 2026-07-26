<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use ModelTrait;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#attribute-casting
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'seo' => 'array',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function permalink()
    {
        return route('tag.show', ['slug' => $this->slug, 'tag' => $this->id]);
    }

    public function feed()
    {
        return route('tag.feed', ['slug' => $this->slug, 'tag' => $this->id]);
    }

    public function getMetaDescription()
    {
        $description = $this->description;

        return $this->getTextChars($description, 160, true);
    }
}
