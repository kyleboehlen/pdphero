<header>
    <h1 id="title">
        <span class="pdp">PDP</span>&nbsp;&nbsp;<span class="hero">HERO</span>
    </h1>
    
    @if(\Auth::check())
        {{-- TO-DO: Change this link to profile --}}
        <a href="{{ route('root') }}">
            <img class="profile" src="{{ asset('icons/profile.png') }}" />
        </a>
    @else
        <a href="{{ route('login') }}"> 
            <button class="login-register">Login/Register</button>
        </a>
    @endif
</header>