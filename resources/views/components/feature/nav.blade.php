<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(in_array('back', $show))
            <a class="close-nav" href="{{ route('feature.list') }}"><li>Back To List</li></a>
        @endif

        <a class="close-nav" href="{{ route('support.email.form') }}"><li>Request Feature</li></a>
    </ul>
</nav>