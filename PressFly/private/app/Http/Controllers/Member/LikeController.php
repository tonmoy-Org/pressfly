<?php

namespace App\Http\Controllers\Member;

use App\Models\Article;

class LikeController extends MemberController
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \auth()->user();

        $articles = $user->likes()
            ->with(['featuredImage', 'user', 'mainCategory'])
            ->whereIn('status', [1, 4])
            ->orderByDesc('likes.id')
            ->paginate(10);

        return \view(
            'member.likes.index',
            [
                'articles' => $articles,
            ]
        );
    }

    public function add(Article $article)
    {
        $article->likes()->sync([\auth()->id()]);
        return \redirect()->back();
    }

    public function remove(Article $article)
    {
        $article->likes()->detach([\auth()->id()]);
        return \redirect()->back();
    }
}
