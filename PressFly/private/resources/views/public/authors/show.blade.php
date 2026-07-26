<?php
/**
 * @var \App\Models\User $user
 */
?>
@extends('layouts.front')

@section('title', $user->name)
@section('description', $user->getMetaDescription())

@push('header')
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{ $user->name }}"/>
    <meta property="og:description" content="{{ $user->getMetaDescription() }}"/>
    <meta property="og:url" content="{{ $user->permalink() }}"/>
    <meta property="og:image" content="{{ $user->profileImage() }}"/>

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $user->name }}">
    <meta name="twitter:description" content="{{ $user->getMetaDescription() }}">
    <meta property="twitter:image" content="{{ $user->profileImage() }}"/>
@endpush

@section('content')
    <main role="main" class="container">

        <div class="row">
            <div class="col-lg-8">
                <div class="col-inner">

                    <div class="author-details">
                        <div class="author-info">
                            <div class="author-image">
                                <img alt="{{ $user->name }}" height="150" width="150" src="{{ $user->profileImage() }}"
                                     loading="lazy">
                            </div>
                            <div class="author-connect">
                                <h1 class="author-name">
                                    {{ $user->name }}
                                </h1>
                                <div class="follow-me">
                                    @if($user->socialNetwork('facebook'))
                                        <a href="{{ $user->socialNetwork('facebook') }}" target="_blank"
                                           title="{{ __('Facebook') }}" class="fab fa-facebook-f fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif

                                    @if($user->socialNetwork('twitter'))
                                        <a href="{{ $user->socialNetwork('twitter') }}" target="_blank"
                                           title="{{ __('Twitter') }}" class="fab fa-twitter fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif

                                    @if($user->socialNetwork('linkedin'))
                                        <a href="{{ $user->socialNetwork('linkedin') }}" target="_blank"
                                           title="{{ __('Linkedin') }}" class="fab fa-linkedin-in fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif

                                    @if($user->socialNetwork('youtube'))
                                        <a href="{{ $user->socialNetwork('youtube') }}" target="_blank"
                                           title="{{ __('YouTube') }}" class="fab fa-youtube fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif

                                    @if($user->socialNetwork('vimeo'))
                                        <a href="{{ $user->socialNetwork('vimeo') }}" target="_blank"
                                           title="{{ __('Vimeo') }}" class="fab fa-vimeo-v fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif
                                    @if($user->socialNetwork('instagram'))
                                        <a href="{{ $user->socialNetwork('instagram') }}" target="_blank"
                                           title="{{ __('Instagram') }}" class="fab fa-instagram fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif
                                    @if($user->socialNetwork('pinterest'))
                                        <a href="{{ $user->socialNetwork('pinterest') }}" target="_blank"
                                           title="{{ __('Pinterest') }}" class="fab fa-pinterest-p fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif
                                    @if($user->socialNetwork('vk'))
                                        <a href="{{ $user->socialNetwork('vk') }}" target="_blank"
                                           title="{{ __('VK') }}" class="fab fa-vk fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif
                                    @if($user->socialNetwork('github'))
                                        <a href="{{ $user->socialNetwork('github') }}" target="_blank"
                                           title="{{ __('GitHub') }}" class="fab fa-github fa-fw"
                                           rel="noopener noreferrer"></a>
                                    @endif
                                </div>

                                <div class="author-description">
                                    {{ $user->description }}
                                </div>

                                <div class="author-follow">
                                    @if(!auth()->check() || !$user->followers()->wherePivot('follower_id', auth()->user()->id)->exists())
                                        <form method="post" action="{{ route('author.follow', [$user->username]) }}">
                                            @csrf
                                            <input type="submit" class="btn btn-primary btn-sm btn-follow"
                                                   value="{{ __('Follow') }}">
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('author.unfollow', [$user->username]) }}">
                                            @csrf
                                            <input type="submit" class="btn btn-primary btn-sm btn-follow"
                                                   value="{{ __('Unfollow') }}">
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row main-listing">
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

                </div>
            </div>
            <div class="col-lg-4">
                <div class="col-inner">
                    {!! \App\Models\Sidebar::sidebarDisplay( get_style('author_sidebar') ) !!}
                </div>
            </div>
        </div>

    </main><!-- /.container -->
@endsection
