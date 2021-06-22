<footer class="app">
    {{-- Home --}}
    @if($home)
        <a class="hover-white" href="{{ route('home') }}">
            <img @if($highlight == 'home') src="{{ asset('icons/home-white.png') }}" @else src="{{ asset('icons/home-black.png') }}" @endif />
        </a>
    @endif

    {{-- Journal --}}
    <a class="hover-white" href="{{ route('journal') }}">
        <img @if($highlight == 'journal') src="{{ asset('icons/journal-white.png') }}" @else src="{{ asset('icons/journal-black.png') }}" @endif />
    </a>

    {{-- Goals --}}
    <a class="hover-white" href="{{ route('goals') }}">
        <img @if($highlight == 'goals') src="{{ asset('icons/goals-white.png') }}" @else src="{{ asset('icons/goals-black.png') }}" @endif />
    </a>

    {{-- Habits --}}
    <a class="hover-white" href="{{ route('habits') }}">
        <img @if($highlight == 'habits') src="{{ asset('icons/habits-white.png') }}" @else src="{{ asset('icons/habits-black.png') }}" @endif />
    </a>

    {{-- To-Do --}}
    <a class="hover-white" href="{{ route('todo.list') }}">
        <img @if($highlight == 'todo') src="{{ asset('icons/todo-white.png') }}" @else src="{{ asset('icons/todo-black.png') }}" @endif />
    </a>
</footer>