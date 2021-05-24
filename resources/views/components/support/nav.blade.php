<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>

    <ul class="list">
        <a class="close-nav" href="{{ route('home') }}"><li>Back To Home</li></a>
        <a class="close-nav" href="{{ route('support.email.form') }}"><li>Send Support Email</li></a>
    </ul>
</nav>