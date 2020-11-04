<footer class="app">
    {{-- Journal --}}
    <a href="{{ route('journal') }}">
        <img class="hover-white" src="{{ asset('icons/journal-white.png') }}" />
    </a>

    {{-- Goals --}}
    <a href="{{ route('goals') }}">
        <img class="hover-white" src="{{ asset('icons/goals-white.png') }}" />
    </a>

    {{-- Habits --}}
    <a href="{{ route('habits') }}">
        <img class="hover-white" src="{{ asset('icons/habits-white.png') }}" />
    </a>

    {{-- To-Do --}}
    <a href="{{ route('todo') }}">
        <img class="hover-white" src="{{ asset('icons/todo-white.png') }}" />
    </a>
</footer>