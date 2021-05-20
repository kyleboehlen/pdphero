@extends('layouts.app')

@section('template')
    <div class="card big-card">
        <h1>{{ __('Verify Your Email Address') }}</h1>

        @if(session('resent'))
            <p class="success">{{ __('A fresh verification link has been sent to your email address.') }}<p>
        @endif

        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
        <p>{{ __('If you did not receive the email') }}:</p>
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf

            <button type="submit">{{ __('click here to request another') }}</button><br/>
        </form>
    </div>
@endsection
