<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'auto' => '',
        'cats' => [],
        'tags' => [],
        'period' => '',
        'num' => 5,
        'class' => '',
    ],
    $widget
);

$category = request()->route()->parameter('category');
$tag = request()->route()->parameter('tag');

$cacheKey = json_encode($widget);
if ($category) {
    $cacheKey .= 'category' . $category->id;
}
if ($tag) {
    $cacheKey .= 'tag' . $tag->id;
}
$cacheKey = 'widgets.popular_articles.v2.' . md5($cacheKey);

$popularArticleIds = \Cache::remember($cacheKey, 1 * 24 * 60 * 60, function () use ($widget, $category, $tag) {
    $auto = (bool)$widget['auto'];
    $routeName = request()->route()->getName();

    $from = null;
    $to = null;

    if ($widget['period'] === 'week') {
        $from = now()->subWeek()->startOfDay()->format('Y-m-d H:i:s');
        $to = now()->endOfDay()->format('Y-m-d H:i:s');
    }

    if ($widget['period'] === 'month') {
        $from = now()->subMonth()->startOfDay()->format('Y-m-d H:i:s');
        $to = now()->endOfDay()->format('Y-m-d H:i:s');
    }

    if ($widget['period'] === 'year') {
        $from = now()->subYear()->startOfDay()->format('Y-m-d H:i:s');
        $to = now()->endOfDay()->format('Y-m-d H:i:s');
    }

    return \App\Models\Article::query()
        ->whereIn('status', [1, 4])
        ->withCount(
            [
                'statistics' => function (\Illuminate\Database\Eloquent\Builder $query) use ($from, $to) {
                    if ($from && $to) {
                        $query->whereBetween('statistics.created_at', [$from, $to]);
                    }
                },
            ]
        )
        ->when(($auto && $routeName === 'category.show' && $category), function ($query) use ($category) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            return $query->whereHas('categories', function ($query) use ($category) {
                /**
                 * @var \Illuminate\Database\Eloquent\Builder $query
                 */
                $query->where('categories.id', $category->id);
            });
        }, function ($query) use ($widget) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            if (isset($widget['cats']) && \count($widget['cats'])) {
                return $query->whereHas('categories', function ($query) use ($widget) {
                    /**
                     * @var \Illuminate\Database\Eloquent\Builder $query
                     */
                    $query->whereIn('categories.id', array_map('intval', $widget['cats']));
                    $query->where('categories.status', 1);
                });
            }
            return $query;
        })
        ->when(($auto && $routeName === 'tag.show' && $tag), function ($query) use ($tag) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            return $query->whereHas('tags', function ($query) use ($tag) {
                /**
                 * @var \Illuminate\Database\Eloquent\Builder $query
                 */
                $query->where('tags.id', $tag->id);
            });
        }, function ($query) use ($widget) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            if (isset($widget['tags']) && count($widget['tags'])) {
                return $query->whereHas('tags', function ($query) use ($widget) {
                    /**
                     * @var \Illuminate\Database\Eloquent\Builder $query
                     */
                    $query->whereIn('tags.id', array_map('intval', $widget['tags']));
                    $query->where('tags.status', 1);
                });
            }
            return $query;
        })
        ->orderByDesc('statistics_count')
        ->limit(intval($widget['num']))
        ->pluck('articles.id')
        ->all();
});

$popularArticleIds = array_values(array_filter(array_map('intval', (array)$popularArticleIds)));

$popular_articles = collect();

if ($popularArticleIds !== []) {
    $articleOrder = array_flip($popularArticleIds);

    $popular_articles = \App\Models\Article::query()
        ->with(['featuredImage', 'user'])
        ->whereIn('id', $popularArticleIds)
        ->get()
        ->sortBy(static function (\App\Models\Article $article) use ($articleOrder) {
            return $articleOrder[$article->id] ?? PHP_INT_MAX;
        })
        ->values();
}
?>

<div class="widget {{ $widget['class'] }}">
    <div class="block-header">
        <div class="block-title"><span>{{ $widget['title'] }}</span></div>
    </div>
    <div class="block-content">
        @foreach($popular_articles as $article)
            <div class="block-item">
                <div class="block-item-img img-side">
                    <a href="{{ $article->permalink() }}" class="b-lazy"
                       title="{{ $article->title }}"
                       data-src="{{ $article->getMainImage('thumbnail') }}"></a>
                </div>
                <div class="block-item-title">
                    <a href="{{ $article->permalink() }}">
                        {{ $article->title }}
                    </a>
                </div>
                <div class="block-item-meta">
                    <small>
                        <i class="far fa-clock"></i> {{ display_date_timezone($article->published_at) }}
                    </small>
                    <small>
                        <i class="far fa-user"></i> {{ $article->user->name }}
                    </small>
                </div>
            </div>
        @endforeach
    </div>
</div>
