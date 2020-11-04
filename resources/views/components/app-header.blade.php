<header class="app">
    <img id="hamburger-nav" class="hamburger hover-white" src="{{ asset('icons/hamburger-white.png') }}" />

    <h1 id="title">
        {{ $title }}
    </h1>
    
    <a href="{{ route('profile') }}">
        {{-- To-Do: Chnage to profile picture route --}}
        <img class="profile" src={{ asset('icons/profile.png') }} />
    </a>
</header>