<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Article[] $articles
 */
?>

@extends('layouts.member')

@section('title', __('My Likes'))

@section('content')
    <div class="main-listing">
        <div class="row">
            <?php $count = 0; ?>
            @foreach($articles as $article)
                @if($count % 5 === 0)
                    <div class="block-item block-item-big col-sm-12 col-lg-12">
                        <div class="block-item-img">
                            <a href="{{ $article->permalink() }}"
                               style="background-image: url('{{ $article->getMainImage('medium') }}')"></a>
                            <div class="block-item-category"
                                 style="background-color: {{ $article->getMainCategory()->color }};">
                                <a href="{{ $article->getMainCategory()->permalink() }}">
                                    {{ $article->getMainCategory()->name }}
                                </a>
                            </div>
                        </div>
                        <div class="block-item-overlay">
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
                                {{ $article->getSummary() }}
                            </div>
                            <a class="read-more"
                               href="{{ $article->permalink() }}">
                                {{ __('Read More') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="block-item col-sm-6 col-lg-6">
                        <div class="block-item-img">
                            <a href="{{ $article->permalink() }}"
                               style="background-image: url('{{ $article->getMainImage('small') }}')"></a>
                            <div class="block-item-category"
                                 style="background-color: {{ $article->getMainCategory()->color }};">
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
                        <a class="read-more"
                           href="{{ $article->permalink() }}">
                            {{ __('Read More') }}
                        </a>
                    </div>
                @endif
                <?php $count++; ?>
            @endforeach
        </div>
        {{ $articles->appends(request()->except(['page']))->links() }}
    </div>
@endsection

