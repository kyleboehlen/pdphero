@extends('layouts.base')

@section('body')
    <body>

        {{-- Info box --}}
        @error('info')
            <x-app.pop-up-box title="Info" :message="$message" />
        @enderror

        {{-- Green success box --}}
        @error('success')
            <x-app.pop-up-box title="Success" :message="$message" />
        @enderror

        {{-- Yellow warning box --}}
        @error('warning')
            <x-app.pop-up-box title="Warning" :message="$message" />
        @enderror

        {{-- Red error box --}}
        @error('error')
            <x-app.pop-up-box title="Error" :message="$message" />
        @enderror

        {{-- Stack errors that bubble up --}}
        @stack('pop-up-boxes')

        @yield('template')

    </body>
@endsection