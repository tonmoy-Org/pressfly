<?php
$articles = \App\Models\Article::with(['user', 'featuredImage', 'mainCategory'])
    ->latest('created_at')
    ->where('status', 1)
    ->paginate();
?>
<style>
    .owl-carousel .item {
        text-align: center;
    }

    .owl-carousel .item-img {
        position: relative;
    }

    .owl-carousel .item-title a {
        font-family: 'Roboto Slab', serif;
        font-size: 20px;
        font-weight: 500;
    }

    .owl-carousel .item-category {
        position: absolute;
        left: 10px;
        top: 10px;
        padding: 0 5px;
        color: #ffffff;
        font-size: 0.75rem;
    }
</style>
<div class="carousel-loop owl-carousel owl-theme">
    @foreach($articles as $article)
        <div class="item">
            <div class="item-img">
                <a href="{{ $article->permalink() }}" title="{{ $article->title }}">
                    <img src="{{ url( $article->getMainImage('medium') ) }}" loading="lazy">
                </a>
                <div class="item-category" style="background-color: {{ (string)$article->getMainCategory()->color }};">
                    <a href="{{ $article->getMainCategory()->permalink() }}">
                        {{ $article->getMainCategory()->name }}
                    </a>
                </div>
            </div>
            <div class="item-title">
                <a href="{{ $article->permalink() }}">{{ $article->title }}</a>
            </div>
            <div class="item-meta">
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
    @endforeach
</div>
