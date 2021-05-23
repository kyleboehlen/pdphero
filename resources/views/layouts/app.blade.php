@extends('layouts.base')

@section('body')
    <body>

        {{-- Mobile install prompt --}}
        @if(\Auth::check() && \Auth::user()->hasVerifiedEmail())
            <div id="install-prompt" @if(config('app.env') == 'local') class="local" @endif>
                <h1>Add PDPHero</h1>
                <p>PDPHero does not require any special permissions, however to use it on mobile it must be launched in standalone mode.</p>
                <p>Follow these instructions and you'll be planning your personal development in no time!</p>
                <h2>On Apple:</h2>
                <ol>
                    <li><img class="apple-share" src="{{ asset('icons/apple-share-white.png') }}"> Click on the share icon in your browser window</li>
                    <li>Click the 'Add to Home Screen' option that you see
                        <br/>
                        <img class="apple-add-to-home-icon" src="{{ asset('images/apple-add-to-home-icon.png') }}"/><br/>
                        <p class="center-text">-- <b>or</b> --</p>
                        <img class="apple-add-to-home-option" src="{{ asset('images/apple-add-to-home-option.png') }}"/><br/>
                    </li>
                    <li>Done! You can now launch PDPHero from the icon that was added to your home screen in standalone mode :)</li>
                </ol>
                <h2>On Android:</h2>
                <ol>
                    <li>If you recieve an 'Add To Home Screen' prompt like this one:<br/><img class="android-add-to-home-prompt" src="{{ asset('images/android-add-to-home.jpg') }}" /><br/>just click that and you're done!</li>
                    <li><img class="android-options" src="{{ asset('icons/android-options-white.png') }}"> Otherwise click the options icon in your browser window</li>
                    <li>Select the 'Add to Home screen' option<br/><img class="android-add-to-home-option" src="{{ asset('images/android-add-to-home-option.png') }}"/></li>
                    <li>Done! You can now launch PDPHero from the icon that was added to your home screen in standalone mode :)</li>
                </ol>
            </div>
        @endif

        {{-- First Visit pop up --}}
        @if(!is_null(session('first_visit_message')))
            <script>
                sweetAlert('Heads Up!', 'info', '#ffffff', "{!! session('first_visit_message') !!}");
            </script>
        @endif

        {{-- Info box --}}
        @error('info')
            <script>
                sweetAlert('Info', 'info', '#ffffff', '{{ $message }}');
            </script>
        @enderror

        {{-- Green success box --}}
        @error('success')
            <script>
                sweetAlert('Success', 'success', '#28d13f', '{{ $message }}');
            </script>
        @enderror

        {{-- Yellow warning box --}}
        @error('warning')
            <script>
                sweetAlert('Warning', 'warning', '#e0dd24', '{{ $message }}');
            </script>
        @enderror

        {{-- Red error box --}}
        @error('error')
            <script>
                sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
            </script>
        @enderror

        {{-- Stack scripts that bubble up --}}
        @stack('scripts')

        @yield('template')

    </body>
@endsection