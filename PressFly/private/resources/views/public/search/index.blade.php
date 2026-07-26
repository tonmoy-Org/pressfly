<?php
/**
 * @var \App\Models\Category $category
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Article[] $articles
 */
?>
@extends('layouts.front')

@section('title', __('Search'))
@section('description', __('Search'))

@section('content')
    <main role="main" class="container">

        <div class="row">
            <div class="col-lg-8">
                <div class="col-inner main-listing">
                    <h3>{{ __('Search results for:') }} {{ $keyword }}</h3>

                    <p>{{ __('If you are not happy with the results below please do another search.') }}</p>

                    <form method="get" action="{{ route('search') }}" class="">
                        <div class="form-group">
                            <input type="text" name="q" placeholder="{{ __('Search keywords') }}" class="form-control"
                                   value="{{ $keyword }}" required>
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="{{ __('Search') }}">
                        </div>
                    </form>

                    @if($keyword)
                        <div class="row">
                            <?php $count = 0; ?>
                            @foreach($articles as $article)
                                @if($count % 5 === 0)
                                    <div class="block-item block-item-big col-sm-12 col-lg-12">
                                        <div class="block-item-img">
                                            <a href="{{ $article->permalink() }}" class="b-lazy"
                                               title="{{ $article->title }}"
                                               data-src="{{ $article->getMainImage('medium') }}"></a>
                                            <div class="block-item-category"
                                                 style="background-color: {{ (string)$article->getMainCategory()->color }};">
                                                <a href="{{ $article->getMainCategory()->permalink() }}">
                                                    {{ $article->getMainCategory()->name }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="block-item-overlay">
                                            <div class="block-item-title">
                                                <a href="{{$article->permalink() }}">
                                                    {{ $article->title }}
                                                </a>
                                            </div>
                                            <div class="block-item-meta">
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
                                            <div class="block-item-content">
                                                {{ $article->getSummary() }}
                                            </div>
                                            <a class="read-more" title="{{ $article->title }}" aria-label="{{ $article->title }}"
                                               href="{{ $article->permalink() }}">
                                                {{ __('Read More') }}
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="block-item col-sm-6 col-lg-6">
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
                                        <div class="block-item-title">
                                            <a href="{{ $article->permalink() }}">
                                                {{ $article->title }}
                                            </a>
                                        </div>
                                        <div class="block-item-meta">
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
                                        <div class="block-item-content">
                                            {{ $article->getSummary(20) }}
                                        </div>
                                        <a class="read-more" title="{{ $article->title }}" aria-label="{{ $article->title }}"
                                           href="{{ $article->permalink() }}">
                                            {{ __('Read More') }}
                                        </a>
                                    </div>
                                @endif
                                <?php $count++; ?>
                            @endforeach
                        </div>

                        <div class="table-responsive">
                            {{ $articles->appends(request()->except(['page']))->links() }}
                        </div>

                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-inner">
                    {!! \App\Models\Sidebar::sidebarDisplay( get_style('search_sidebar') ) !!}
                </div>
            </div>
        </div>

    </main><!-- /.container -->
@endsection
