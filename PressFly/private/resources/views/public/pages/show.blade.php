<?php
/**
 * @var \App\Models\Page $page
 */

?>
@extends('layouts.front')

@php
    $seoTitle = $page->seo['title'] ?? $page->title;
    $seoDescription = $page->seo['description'] ?? $page->getMetaDescription();
    $seoKeywords = (!empty($page->seo['keywords'])) ? $page->seo['keywords'] : null;
@endphp
@section('title', $seoTitle)
@section('description', $seoDescription)
@section('keywords', $seoKeywords)

@push('header')
    <script type="application/ld+json"><?php
        echo json_encode(
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $seoTitle,
                'description' => $seoDescription,
                "datePublished" => $page->created_at->toIso8601String(),
                "dateModified" => $page->updated_at->toIso8601String(),
            ]
        );
        ?></script>
@endpush

@section('content')
    <main role="main" class="container">

        <h1 class="page-title">{{ $page->title }}</h1>

        <div id="main-content" class="page-content dont-break-out">
            {!! applyShortCodes($page->content) !!}
        </div>

    </main><!-- /.container -->
@endsection
