<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
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
        'status' => 'integer',
        'seo' => 'array',
    ];

    public function permalink()
    {
        return route('page.show', ['slug' => $this->slug]);
    }

    public function getMetaDescription()
    {
        $content = $this->content;

        return $this->getTextChars($content, 160, true);
    }
}
