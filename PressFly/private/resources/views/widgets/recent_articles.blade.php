<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'cats' => [],
        'tags' => [],
        'num' => 5,
        'class' => '',
    ],
    $widget
);

$recent_articles = \App\Models\Article::query()
    ->with(['user', 'featuredImage'])
    ->when((isset($widget['cats']) && count($widget['cats'])), function ($query) use ($widget) {
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        return $query->whereHas('categories', function ($query) use ($widget) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            $query->whereIn('id', array_map('intval', $widget['cats']));
            $query->where('status', 1);
        });
    })
    ->when((isset($widget['tags']) && count($widget['tags'])), function ($query) use ($widget) {
        /**
         * @var \Illuminate\Database\Eloquent\Builder $query
         */
        return $query->whereHas('tags', function ($query) use ($widget) {
            /**
             * @var \Illuminate\Database\Eloquent\Builder $query
             */
            $query->whereIn('id', array_map('intval', $widget['tags']));
            $query->where('status', 1);
        });
    })
    ->whereIn('status', [1, 4])
    ->orderBy('published_at', 'desc')
    ->limit(intval($widget['num']))
    ->get();
?>
<div class="widget">
    <div class="block-header">
        <div class="block-title"><span>{{ __('Recent Articles') }}</span></div>
    </div>
    <div class="block-content">
        @foreach($recent_articles as $article)
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
