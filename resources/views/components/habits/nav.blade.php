<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('habits') }}"><li>Back To Habits</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('habits.create') }}"><li class="top">Create New Habit</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('habits.edit', ['habit' => $habit->uuid]) }}"><li>Edit Habit</li></a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-habit-form" action="{{ route('habits.destroy', ['habit' => $habit->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('todo.destroy', ['todo' => $item->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); document.getElementById('delete-item-form').submit();">
                <li>Delete Item</li>
            </a>
        @endif 
    </ul>
</nav>