<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        <a href="{{ route('profile') }}"><li>Back To Profile</li></a>
    </ul>
</nav>