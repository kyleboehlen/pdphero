@extends('layouts.app')

@section('template')
    <div class="card">
        <h1>{{ __('Login') }}</h1>

        <form method="POST" action="{{ route('login') }}">
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
            
            <div>
                <label for="password">{{ __('Password') }}</label>

                <div>
                    <input id="password" type="password" name="password" required autocomplete="current-password">

                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div><br/>
            
            <div>
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                &nbsp;
                <label class="checkbox" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div><br/>
            

            <button type="submit">
                {{ __('Login') }}
            </button><br/><br/>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot Password?</a><br/><br/>
            @endif

            @if (Route::has('register'))
                <a href="{{ route('register') }}">No Account? Sign Up.</a><br/><br/><br/>
            @endif
        </form>
    </div>
@endsection
