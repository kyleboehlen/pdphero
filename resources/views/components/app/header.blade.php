<header class="app">
    <img id="hamburger-nav" class="hamburger hover-white" src="{{ asset('icons/hamburger-black.png') }}" />

    <h1 id="title">
        {{ $title }}
    </h1>
    
    <a href="{{ route($route) }}">
        <img class="profile" src={{ asset($icon) }} />
    </a>
</header>