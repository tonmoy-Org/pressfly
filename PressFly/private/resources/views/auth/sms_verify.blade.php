@extends('layouts.auth')

@section('title', __('SMS Verification'))
@section('description', __('Verify your mobile number'))

@section('content')

    <form id="sms-verify-form" method="POST" action="{{ route('sms.verify') }}">
        @csrf

        <div class="form-group">
            <input id="sms_code" type="text"
                   class="form-control form-control-sm{{ session('danger') ? ' is-invalid' : '' }}"
                   name="sms_code" placeholder="{{ __('6-digit Verification Code') }}" required autofocus>

            @if (session('danger'))
                <span class="invalid-feedback" role="alert"><strong>{{ session('danger') }}</strong></span>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block btn-sm">
                {{ __('Verify Code') }}
            </button>
        </div>
        
        <div class="form-group mt-3 text-center">
            <p>
                @lang("If you don't get any code"), 
                <a href="{{ route('sms.resend') }}"><u>@lang('Resend Code')</u></a>
            </p>
        </div>
    </form>
    
@endsection
