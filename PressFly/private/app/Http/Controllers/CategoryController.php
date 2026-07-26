<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @param \App\Models\Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(string $slug, Category $category)
    {
        if (!$category->status) {
            abort(404);
        }

        if ($slug !== $category->slug) {
            return \redirect($category->permalink(), 301);
        }

        /*
        $cats = [$category->id];
        $childCats = Category::getChildrenCategoriesIds($category->id);
        if (\count($childCats)) {
            $cats = $cats + $childCats;
        }
        $cats = \array_unique($cats);

        $articles = \App\Models\Article::query()
            ->whereHas(
                'categories',
                function ($query) use ($cats) {
                    if (!empty($cats)) {
                        $query->whereIn('categories.id', $cats);
                        $query->where('categories.status', 1);
                    }
                }
            )
            ->whereIn('status', [1, 4])
            ->orderByDesc('published_at')
            ->paginate(10);
        */

        $articles = $category->articles()
            ->with(['featuredImage', 'user', 'mainCategory'])
            ->whereIn('status', [1, 4])
            ->orderByDesc('published_at')
            ->paginate(10);

        return \view(
            'public.categories.show',
            [
                'category' => $category,
                'articles' => $articles,
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @param \App\Models\Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function feed(string $slug, Category $category)
    {
        if (!$category->status) {
            abort(404);
        }

        if ($slug !== $category->slug) {
            return redirect($category->feed(), 301);
        }

        $articles = $category->articles()
            ->whereIn('status', [1, 4])
            ->orderByDesc('published_at')
            ->limit(15)
            ->get();

        return response()
            ->view(
                'public.feed',
                [
                    'info' => [
                        'title' => $category->name . ' - ' . get_option('site_name'),
                        'atom_link' => $category->feed(),
                        'description' => $category->description ?? get_option('site_description'),
                    ],
                    'articles' => $articles,
                ]
            )
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
