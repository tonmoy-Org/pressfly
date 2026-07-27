<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ get_option('language_direction', 'ltr') }}"
      class="{{ get_option('language_direction', 'ltr') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <meta name="description" content="@yield('description') )">
    <meta name="keywords" content="@yield('keywords', get_option('site_keywords') )">
    <link rel="canonical" href="{{ url()->current() }}"/>

    <link href='{{ asset(get_style('favicon', '/favicon.ico')) }}' type='image/x-icon' rel='icon'/>
    <link href='{{ asset(get_style('favicon', '/favicon.ico')) }}' type='image/x-icon' rel='shortcut icon'/>

    @if(get_option('language_direction', 'ltr') === 'rtl')
        <link href="https://fastly.jsdelivr.net/gh/RTLCSS/bootstrap@4.5.3-rtl/dist/css/rtl/bootstrap.min.css"
              rel="stylesheet">
    @else
        <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    @endif
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/css/auth.css?v=' . APP_VERSION) }}" rel="stylesheet">

    @stack('header')
</head>
<body>

<div class="auth">
    <div class="auth-title">
        <a href="{{ url('/') }}">
            @if(get_style('logo_image'))
                <img src="{{ asset(get_style('logo_image')) }}" alt="{{ get_option('site_name') }}"
                     width="{{ get_style('logo_width') }}" height="{{ get_style('logo_height') }}">
            @else
                {{ get_option('site_name') }}
            @endif
        </a>
    </div>

    <div class="auth-content">
        @include('_partials.flash_message')

        @yield('content')
    </div>
</div>

@include('_partials.js_vars')

<script data-cfasync="false" src="{{ asset('assets/js/ads.js') }}"></script>

<script src="https://fastly.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/selection-sharer@1.2.2/dist/selection-sharer.js"></script>
<script src="https://fastly.jsdelivr.net/gh/ppowalowski/stickUp2@2.3.2/build/js/stickUp.min.js"></script>

<script src="{{ asset('assets/js/app.js?v=' . APP_VERSION) }}"></script>

@include('_partials.visitor_check')

@stack('footer')

</body>
</html>
