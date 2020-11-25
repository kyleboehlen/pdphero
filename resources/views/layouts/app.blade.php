@extends('layouts.base')

@section('body')
    <body>

        {{-- Info box --}}
        @error('info')
            <div class="info-box">
                <h1>Info</h1>
                <p>{{ $message }}</p>
                <button class="close-box" type="button">Okay</button><br/><br/>
            </div>
        @enderror

        {{-- Green success box --}}
        @error('success')
            <div class="success-box">
                <h1>Success</h1>
                <p>{{ $message }}</p>
                <button class="close-box" type="button">Okay</button><br/><br/>
            </div>
        @enderror

        {{-- Yellow warning box --}}
        @error('warning')
            <div class="warning-box">
                <h1>Warning</h1>
                <p>{{ $message }}</p>
                <button class="close-box" type="button">Okay</button><br/><br/>
            </div>
        @enderror

        {{-- Red error box --}}
        @error('error')
            <div class="error-box">
                <h1>Error</h1>
                <p>{{ $message }}</p>
                <button class="close-box" type="button">Okay</button><br/><br/>
            </div>
        @enderror

        @yield('template')

    </body>
@endsection