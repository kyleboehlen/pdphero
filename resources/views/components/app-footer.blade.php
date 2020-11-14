<footer class="app">
    {{-- Journal --}}
    <a @if($highlight == 'journal') class="hover-black" @else class="hover-white" @endif href="{{ route('journal') }}">
        <img @if($highlight == 'journal') src="{{ asset('icons/journal-black.png') }}" @else src="{{ asset('icons/journal-white.png') }}" @endif />
    </a>

    {{-- Goals --}}
    <a @if($highlight == 'goals') class="hover-black" @else class="hover-white" @endif href="{{ route('goals') }}">
        <img @if($highlight == 'goals') src="{{ asset('icons/goals-black.png') }}" @else src="{{ asset('icons/goals-white.png') }}" @endif />
    </a>

    {{-- Habits --}}
    <a @if($highlight == 'habits') class="hover-black" @else class="hover-white" @endif href="{{ route('habits') }}">
        <img @if($highlight == 'habits') src="{{ asset('icons/habits-black.png') }}" @else src="{{ asset('icons/habits-white.png') }}" @endif />
    </a>

    {{-- To-Do --}}
    <a @if($highlight == 'todo') class="hover-black" @else class="hover-white" @endif href="{{ route('todo') }}">
        <img @if($highlight == 'todo') src="{{ asset('icons/todo-black.png') }}" @else src="{{ asset('icons/todo-white.png') }}" @endif />
    </a>
</footer>