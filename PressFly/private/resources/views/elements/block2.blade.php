<?php
/**
 * @var \Illuminate\Pagination\Paginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Article[] $articles
 */

$articles = \App\Models\Article::getArticles($attributes);
$categories = \App\Models\Category::whereIn('id', array_map('trim', explode(',', $attributes['cats'])))->get();
?>

<div class="block block2" id="b-{{ uniqid() }}" data-action="block2"
     data-perPage="{{ $attributes['per_page'] }}"
     data-spinner="{{ $attributes['spinner'] }}"
     data-cats="{{ $attributes['cats'] }}"
     data-summaryLength="{{ $attributes['summary_length'] }}"
     data-pagination="{{ $attributes['pagination'] }}" data-loadedPages="1" data-currentPage="1"
     data-orderBy="{{ $attributes['order_by'] }}"
     data-order="{{ $attributes['order'] }}">

    <div class="block-header">
        <div class="block-title"><span>{{ $attributes['title'] }}</span></div>
        <div class="block-cats">
            @if( $attributes['filter'] === 'yes' && count($categories))
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a class="nav-cat" href="#" data-category="{{ $attributes['cats'] }}">
                            {{ __('All') }}
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li class="list-inline-item">
                            <a class="nav-cat" href="#" data-category="{{ $category->id }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <div class="block-content">
        <div data-loadedPage="{{ $attributes['page'] }}">

            <div class="row">
                <?php $count = 1; ?>
                @foreach($articles as $article)
                    @if($count === 1)
                        <div class="block-item block-item-big col-sm-12 col-lg-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="block-item-img">
                                        <a href="{{ $article->permalink() }}" class="b-lazy"
                                           title="{{ $article->title }}"
                                           data-src="{{ $article->getMainImage('small') }}"></a>
                                        <div class="block-item-category"
                                             style="background-color: {{ (string)$article->getMainCategory()->color }};">
                                            <a href="{{ $article->getMainCategory()->permalink() }}">
                                                {{ $article->getMainCategory()->name }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="block-item-title">
                                        <a href="{{ $article->permalink() }}">
                                            {{ $article->title }}
                                        </a>
                                    </div>

                                    @include('public._partials.listing_meta_data', ['article' => $article])

                                    <div class="block-item-content">
                                        {{ $article->getSummary(60) }}
                                    </div>
                                    <a class="read-more" title="{{ $article->title }}" aria-label="{{ $article->title }}"
                                       href="{{ $article->permalink() }}">
                                        {{ __('Read More') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(!in_array($count, [1]))
                        <div class="block-item col-sm-6 col-lg-4">
                            <div class="block-item-img">
                                <a href="{{ $article->permalink() }}" class="b-lazy" title="{{ $article->title }}"
                                   data-src="{{ $article->getMainImage('small') }}"></a>
                                <div class="block-item-category"
                                     style="background-color: {{ (string)$article->getMainCategory()->color }};">
                                    <a href="{{ $article->getMainCategory()->permalink() }}">
                                        {{ $article->getMainCategory()->name }}
                                    </a>
                                </div>
                            </div>
                            <div class="block-item-title">
                                <a href="{{ $article->permalink() }}">
                                    {{ $article->title }}
                                </a>
                            </div>

                            @include('public._partials.listing_meta_data', ['article' => $article])
                        </div>
                    @endif
                    <?php $count++; ?>
                @endforeach
            </div>

            {{-- $articles->appends(request()->except(['page']))->links('pagination.'.$attributes['pagination']) --}}

        </div>
    </div>
</div>
