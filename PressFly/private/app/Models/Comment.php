<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#attribute-casting
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'article_id' => 'integer',
        'status' => 'integer',
        'parent_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id')->withDefault();
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('replies')// nest through children
            ->orderBy('id');
    }

    public function activeReplies()
    {
        return $this->replies()->with('user')->where('status', 1)->get();
    }

    public static function getChildrenRepliesIds($id)
    {
        $ids = [];

        $categories = self::query()->where('parent_id', $id)->select(['id'])->get();
        if (count($categories)) {
            foreach ($categories as $category) {
                $ids[$category->id] = $category->id;

                $ids = $ids + self::getChildrenRepliesIds($category->id);
            }
        }
        return $ids;
    }
}
