<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('goals') }}"><li>Back To Goals</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('goals.create.goal') }}"><li>Create New Goal</li></a>
        @endif

        @if(in_array('categories', $show))
            <a href="{{ route('goals.categories') }}"><li>Goal Categories</li></a>
        @endif
    </ul>
</nav>