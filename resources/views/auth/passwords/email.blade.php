@extends('layouts.app')

@section('template')
    <div class="card">
        <h1>{{ __('Reset Password') }}</h1>

        @if(session('status'))
            <p class="success">{{ session('status') }}</p>
        @endif
        
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <label for="email">{{ __('E-Mail Address') }}</label>
                <div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div><br/>

            <button class="reset-link" type="submit">
                {{ __('Send Link') }}
            </button><br/>
        </form>
    </div>
@endsection
