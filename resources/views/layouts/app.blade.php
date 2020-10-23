<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#155466">
    <meta name="apple-mobile-web-app-title" content="PDPHero">
    <meta name="application-name" content="PDPHero">
    <meta name="msapplication-TileColor" content="#26130c">
    <meta name="theme-color" content="#26130c">
</head>
<body>
    <div id="app">
        <nav>
            <a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
            @guest
                <a href="{{ route('login') }}">{{ __('Login') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">{{ __('Register') }}</a>
                @endif
            @else
                <a href="#">
                    {{ Auth::user()->name }}
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">{{ __('Logout') }}</button>
                </form>
            @endguest
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
