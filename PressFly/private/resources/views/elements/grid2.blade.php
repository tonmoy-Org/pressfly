<?php
/**
 * @var \Illuminate\Pagination\Paginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Article[] $articles
 */

$articles = \App\Models\Article::getArticles($attributes);
$categories = \App\Models\Category::whereIn('id', array_map('trim', explode(',', $attributes['cats'])))->get();
?>

<div class="grid grid2" id="b-{{ uniqid() }}" data-action="grid2"
     data-perPage="{{ $attributes['per_page'] }}"
     data-spinner="{{ $attributes['spinner'] }}"
     data-cats="{{ $attributes['cats'] }}"
     data-summaryLength="{{ $attributes['summary_length'] }}"
     data-pagination="{{ $attributes['pagination'] }}" data-loadedPages="1" data-currentPage="1"
     data-orderBy="{{ $attributes['order_by'] }}"
     data-order="{{ $attributes['order'] }}">

    @if($attributes['title'] === 'yes')
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
    @endif

    <div class="block-content">
        <div data-loadedPage="{{ $attributes['page'] }}">

            <div class="grid-items-list">
                @foreach($articles as $article)
                    <div class="grid-item">
                        <div class="grid-item-img">
                            <a href="{{ $article->permalink() }}" class="b-lazy" title="{{ $article->title }}"
                               data-src="{{ $article->getMainImage('medium') }}"></a>
                            <div class="block-item-category"
                                 style="background-color: {{ (string)$article->getMainCategory()->color }};">
                                <a href="{{ $article->getMainCategory()->permalink() }}">
                                    {{ $article->getMainCategory()->name }}
                                </a>
                            </div>
                            <div class="grid-item-overlay">
                                <div class="grid-item-title">
                                    <a href="{{ $article->permalink() }}">
                                        {{ $article->title }}
                                    </a>
                                </div>
                                <div class="grid-item-meta">
                                    @if(in_array('hits', get_option('listing_meta_data', [])))
                                        <small data-toggle="tooltip" data-placement="top"
                                               title="{{ __('Views') }}">
                                            <i class="far fa-eye"></i> {{ display_number($article->hits) }} {{ __('Hits') }}
                                        </small>
                                    @endif
                                    @if(in_array('paid_views', get_option('listing_meta_data', [])))
                                        <small data-toggle="tooltip" data-placement="top"
                                               title="{{ __('Views') }}">
                                            <i class="far fa-eye"></i> {{ display_number($article->paidViews()) }} {{ __('Hits') }}
                                        </small>
                                    @endif
                                    @if(in_array('author', get_option('listing_meta_data', [])))
                                        <small data-toggle="tooltip" data-placement="top"
                                               title="{{ __('Author') }}">
                                            <i class="far fa-user"></i> {{ $article->user->name }}
                                        </small>
                                    @endif
                                    @if(in_array('published_date', get_option('listing_meta_data', [])))
                                        <small data-toggle="tooltip" data-placement="top"
                                               title="{{ __('Published on') }}">
                                            <i class="far fa-clock"></i> {{ display_date_timezone($article->published_at) }}
                                        </small>
                                    @endif
                                    @if(in_array('modified_date', get_option('listing_meta_data', [])))
                                        <small data-toggle="tooltip" data-placement="top"
                                               title="{{ __('Updated on') }}">
                                            <i class="far fa-edit"></i> {{ display_date_timezone($article->updated_at) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- $articles->appends(request()->except(['page']))->links('pagination.'.$attributes['pagination']) --}}

        </div>
    </div>
</div>
