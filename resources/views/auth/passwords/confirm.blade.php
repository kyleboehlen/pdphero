@extends('layouts.app')

@section('template')
    <div class="card">
        <h1>{{ __('Confirm Password') }}</h1>
        <p>{{ __('Please confirm your password before continuing.') }}</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <label for="password">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit">
                {{ __('Confirm Password') }}
            </button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </form>
    </div>
@endsection
