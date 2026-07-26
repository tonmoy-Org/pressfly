@extends('layouts.front')

@section('title', __('Contact us'))
@section('description', __('Contact us'))

@section('content')
    <main role="main" class="container">

        <div class="block-header">
            <div class="block-title"><span>{{ __('Contact Us') }}</span></div>
        </div>

        <form id="contact-form" method="post" action="{{ route('contact.process') }}">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="body">{{ __('Message') }}</label>
                <textarea name="body" id="body" class="form-control" rows="10" required>{{ old('body') }}</textarea>
            </div>

            @if ((bool)get_option('captcha_contact', 0) && isset_captcha())
                <div class="form-group captcha" style="justify-content: normal;">
                    <div id="captchaContact" style="display: inline-block;"></div>
                </div>
            @endif

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
            </div>
        </form>

    </main><!-- /.container -->
@endsection
