@extends('layouts.base')

@section('body')
    <body>

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
        <script>
            @stack('scripts')
        </script>

        @yield('template')

    </body>
@endsection