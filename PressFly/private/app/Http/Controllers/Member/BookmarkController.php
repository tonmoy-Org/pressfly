<?php

namespace App\Http\Controllers\Member;

use App\Models\Article;

class BookmarkController extends MemberController
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = \auth()->user();

        $articles = $user->bookmarks()
            ->with(['featuredImage', 'user', 'mainCategory'])
            ->whereIn('status', [1, 4])
            ->orderByDesc('bookmarks.id')
            ->paginate(10);

        return \view(
            'member.bookmarks.index',
            [
                'articles' => $articles,
            ]
        );
    }

    public function add(Article $article)
    {
        $article->bookmarks()->sync([\auth()->id()]);
        return \redirect()->back();
    }

    public function remove(Article $article)
    {
        $article->bookmarks()->detach([\auth()->id()]);
        return \redirect()->back();
    }
}
