<?php
/**
 * @var \App\Models\Article $article
 */

?>

@extends('layouts.front')

@php
    $seoTitle = $article->seo['title'] ?? $article->title;
    $seoDescription = $article->seo['description'] ?? $article->getMetaDescription();
    $seoKeywords = (!empty($article->seo['keywords'])) ? $article->seo['keywords'] : null;
@endphp
@section('title', $seoTitle)
@section('description', $seoDescription)
@section('keywords', $seoKeywords)

@push('header')
    <meta property="og:type" content="article"/>
    <meta property="article:section" content="{{ $article->getMainCategory()->name }}"/>
    @if($article->published_at)
        <meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}"/>
    @endif
    <meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}"/>
    <meta property="og:url" content="{{ $article->permalink() }}"/>
    <meta property="og:title" content="{{ $seoTitle }}"/>
    <meta property="og:description" content="{{ $seoDescription }}"/>
    <meta property="og:image" content="{{ asset($article->getMainImage('large')) }}"/>
    <meta property="og:image:width" content="1024"/>
    <meta property="og:image:height" content="615"/>

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article->title }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ asset($article->getMainImage('large')) }}">

    <script type="text/javascript">
        if (window.self !== window.top) {
            window.top.location.href = window.location.href;
        }
    </script>

    <script type="application/ld+json">
        @php
            echo json_encode(
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'name' => $article->title,
                    'description' => $article->summary,
                    'headline' => $article->summary,
                    'image' => [
                        asset($article->getMainImage('large')),
                        asset($article->getMainImage('medium')),
                        asset($article->getMainImage('small')),
                        asset($article->getMainImage('thumbnail')),
                    ],
                    "author" => [
                        "@context" => "https://schema.org",
                        "@type" => "Person",
                        "url" => $article->user->permalink(),
                        "name" => $article->user->name,
                    ],
                    "datePublished" => $article->published_at ? $article->published_at->toIso8601String(
                    ) : '',
                    "dateModified" => $article->updated_at->toIso8601String(),
                ]
            );
        @endphp
    </script>
@endpush

@push('body_class', ' article-show')

@section('content')
    <div class="article-main-image-bg b-lazy"
         data-src="{{ asset($article->getMainImage('large')) }}">
        <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
             data-src="{{ asset($article->getMainImage('large')) }}" alt="{{ $article->title }}"
             width="1024" height="615" class="b-lazy" style="aspect-ratio: 1024 / 615;"/>
    </div>
    <main role="main" class="container article-main-content">
        <div class="row">
            <div class="col-lg-8">
                <div class="col-inner">
                    @include('public._partials.article_meta_data', ['article' => $article])

                    <h1 class="article-title">{{ $article->title }}</h1>

                    <div class="d-inline-flex" style="margin-bottom: 10px;">
                        @if(!auth()->check() || !auth()->user()->likes()->wherePivot('article_id', $article->id)->count())
                            <form method="post" action="{{ route('member.like.add', $article->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm mr-1">
                                    <i class="far fa-thumbs-up"></i> {{ __('Like') }}
                                </button>
                            </form>
                        @else
                            <form method="post" action="{{ route('member.like.remove', $article->id) }}">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-light btn-sm active mr-1">
                                    <i class="far fa-thumbs-up"></i> {{ __('Liked') }}
                                </button>
                            </form>
                        @endif

                        @if(!auth()->check() || !auth()->user()->bookmarks()->wherePivot('article_id', $article->id)->count())
                            <form method="post" action="{{ route('member.bookmark.add', $article->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm mr-1">
                                    <i class="far fa-bookmark"></i> {{ __('Bookmark') }}
                                </button>
                            </form>
                        @else
                            <form method="post" action="{{ route('member.bookmark.remove', $article->id) }}">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-light btn-sm active mr-1">
                                    <i class="far fa-bookmark"></i> {{ __('Bookmarked') }}
                                </button>
                            </form>
                        @endif

                        <button type="button" class="article-share-button btn btn-light btn-sm">
                            <i class="fas fa-share-alt"></i> {{ __('Share') }}
                        </button>
                        @include('public._partials.share_modal', ['article' => $article])
                    </div>

                    <?= applyShortCodes('[ads id="' . get_style('above_article_ad') . '"]') ?>

                    <div id="main-content" class="article-content dont-break-out">
                        {!! $article->getFinalContent() !!}
                    </div>

                    <?= applyShortCodes('[ads id="' . get_style('below_article_ad') . '"]') ?>

                    <script>
                        /* <![CDATA[ */
                        var read_time = <?= $article->read_time ?>;
                        /* ]]> */
                    </script>
                    <form action="{{ route('article-go') }}" method="post" id="view-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="view_form_data" value="{{ $view_form_data }}">
                    </form>

                    <div class="article-tags">
                        <span
                            class="badge badge-secondary">{{ __('Explore more about') }}</span> {!! $article->tagsString() !!}
                    </div>

                    <div class="article-newsletter">
                        <p>
                            <i class="far fa-envelope"></i> {{ __('Enjoyed this article? Stay informed by joining our newsletter!') }}
                        </p>

                        <form method="post" action="{{ route('newsletter.subscribe') }}"
                              class="newsletter-subscribe form-inline">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" placeholder="mail@example.com" class="form-control"
                                       required>
                            </div>

                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="{{ __('Subscribe') }}">
                            </div>
                        </form>
                    </div>

                    <div id="comments" class="article-comments mb-3">
                        <div class="block-header">
                            <div class="block-title"><span>{{ __('Comments') }}</span></div>
                        </div>

                        @if($article->activeComments()->count())
                            <div class="comments">
                                @include('public._partials._comment_replies', ['comments' => $article->activeComments(), 'article_id' => $article->id])
                            </div>
                        @endif

                        @guest
                            <p>{{ __('You must be logged in to post a comment.') }}</p>
                        @else
                            <div id="add-comment">
                                <h4>{{ __('Add comment') }}</h4>
                                <form class="form-comment" method="post" action="{{ route('comment.add') }}">
                                    @csrf
                                    <input type="hidden" name="data" value="<?= encrypt(
                                        [
                                            'article_id' => $article->id,
                                            'parent_id' => null,
                                        ]
                                    ) ?>"/>

                                    <div class="form-group">
                                    <textarea required name="content" class="form-control"
                                              placeholder="{{ __('Content') }}"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                </form>
                            </div>
                        @endguest
                    </div>

                    @if($relatedArticles = $article->relatedArticles())
                        <div class="widget article-related mb-3">
                            <div class="block-header">
                                <div class="block-title"><span>{{ __('Related Articles') }}</span></div>
                            </div>
                            <div class="block-content">
                                <div class="row">
                                    @foreach($relatedArticles as $article)
                                        <div class="block-item col-sm-6 col-lg-4">
                                            <div class="block-item-img">
                                                <a href="{{ $article->permalink()}}" title="{{ $article->title }}"
                                                   class="b-lazy" data-src="{{ $article->getMainImage('small') }}"></a>
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
                                                <small>
                                                    <i class="far fa-clock"></i> {{ display_date_timezone($article->published_at) }}
                                                </small>
                                                -
                                                <small><i class="far fa-user"></i> {{ $article->user->name }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 sticky-element">
                <div class="col-inner">
                    {!! \App\Models\Sidebar::sidebarDisplay( get_style('article_sidebar') ) !!}
                </div>
            </div>
        </div>

    </main><!-- /.container -->
@endsection
