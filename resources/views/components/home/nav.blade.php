<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('home') }}"><li>Back To Home</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('home.edit') }}"><li class="top">Hide/Show Icons</li></a>
        @endif
    </ul>
</nav>