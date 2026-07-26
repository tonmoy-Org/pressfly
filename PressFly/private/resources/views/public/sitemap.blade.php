<?php
/**
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\Page[] $pages
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\Article[] $articles
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\Category[] $categories
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\Tag[] $tags
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\User[] $users
 */
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
    </url>
    @foreach($pages as $page)
        <url>
            <loc>{{ $page->permalink() }}</loc>
        </url>
    @endforeach
    @foreach($articles as $article)
        <url>
            <loc>{{ $article->permalink() }}</loc>
            <lastmod>{{ $article->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        </url>
    @endforeach
    @foreach($categories as $category)
        <url>
            <loc>{{ $category->permalink() }}</loc>
        </url>
    @endforeach
    @foreach($tags as $tag)
        <url>
            <loc>{{ $tag->permalink() }}</loc>
        </url>
    @endforeach
    @foreach($users as $user)
        <url>
            <loc>{{ $user->permalink() }}</loc>
        </url>
    @endforeach
</urlset>
