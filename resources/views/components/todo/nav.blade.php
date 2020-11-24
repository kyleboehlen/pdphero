<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close" src="{{ asset('icons/close-white.png') }}" />
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    @switch($page)
        @case('create')
            <ul class="list">
                <a href="{{ route('todo.list') }}"><li>Back To List</li></a>

                <a href="{{ route('todo.create', ['from' => 'habit']) }}"><li>Create From Habit</li></a>

                <a href="{{ route('todo.create', ['from' => 'goal']) }}"><li>Create From Goal</li></a>
            </ul>
            @break
        @case('list')
            <ul class="list">
                <a href="{{ route('todo.create') }}"><li class="top">Create New To-Do Item</li></a>

                <a href="{{ route('todo.create', ['from' => 'habit']) }}"><li>Create From Habit</li></a>

                <a href="{{ route('todo.create', ['from' => 'goal']) }}"><li>Create From Goal</li></a>
            </ul>
            @break
        @case('edit')
            
            @break
        @default
            <ul class="default">
                <a href="{{ route('todo.list') }}"><li>Back To List</li></a>
            </ul>
    @endswitch
</nav>