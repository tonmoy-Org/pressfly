<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'period' => '',
        'num' => 5,
        'class' => '',
    ],
    $widget
);
?>

<?php
/**
 * @var \App\Models\User $user
 */
$username = request()->route()->parameter('username');
if (!$username) {
    return;
}
$user = \App\Models\User::whereUsername($username)->first();
if (!$user) {
    return;
}

$cacheKey = 'widgets.author_popular.v2.' . md5(json_encode($widget) . 'user' . $user->id);

$popularArticleIds = \Cache::remember($cacheKey, 1 * 24 * 60 * 60, function () use ($widget, $user) {
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
        ->where('user_id', $user->id)
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

<div class="widget">
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
                    <a href="{{ $article->permalink() }}">{{ $article->title }}</a>
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
