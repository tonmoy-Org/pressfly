@extends('layouts.front')

@push('header')
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @if(get_option('site_share_image'))
        <meta property="og:image" content="{{ asset(get_option('site_share_image')) }}"/>
    @endif
@endpush

@section('content')

    <main role="main" id="main-content">
        {!! $content !!}
    </main>

@endsection
