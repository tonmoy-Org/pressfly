<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function show(string $slug, Tag $tag)
    {
        if (!$tag->status) {
            abort(404);
        }

        if ($slug !== $tag->slug) {
            return \redirect($tag->permalink(), 301);
        }

        $articles = $tag->articles()
            ->with(['featuredImage', 'user', 'mainCategory'])
            ->whereIn('status', [1, 4])
            ->orderByDesc('published_at')
            ->paginate(10);

        return \response()
            ->view(
                'public.tags.show',
                [
                    'tag' => $tag,
                    'articles' => $articles,
                ]
            );
    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function feed(string $slug, Tag $tag)
    {
        if (!$tag->status) {
            abort(404);
        }

        if ($slug !== $tag->slug) {
            return redirect($tag->feed(), 301);
        }

        $articles = $tag->articles()
            ->whereIn('status', [1, 4])
            ->orderByDesc('published_at')
            ->limit(15)
            ->get();

        return response()
            ->view(
                'public.feed',
                [
                    'info' => [
                        'title' => $tag->name . ' - ' . get_option('site_name'),
                        'atom_link' => $tag->feed(),
                        'description' => $tag->description ?? get_option('site_description'),
                    ],
                    'articles' => $articles,
                ]
            )
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
