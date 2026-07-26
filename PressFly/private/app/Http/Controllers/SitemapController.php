<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;

class SitemapController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::query()
            ->select(['id', 'slug'])
            ->where('status', 1)
            ->get();

        $tags = Tag::query()
            ->select(['id', 'slug'])
            ->where('status', 1)
            ->get();

        $pages = Page::query()
            ->select(['id', 'slug'])
            ->where('status', 1)
            ->get();

        $users = User::query()
            ->select(['username'])
            ->where('status', 1)
            ->get();

        $articles = Article::query()
            ->select(['id', 'slug', 'updated_at'])
            ->whereIn('status', [1, 4])
            ->get();

        return \response()
            ->view(
                'public.sitemap',
                [
                    'categories' => $categories,
                    'tags' => $tags,
                    'pages' => $pages,
                    'users' => $users,
                    'articles' => $articles,
                ]
            )
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
