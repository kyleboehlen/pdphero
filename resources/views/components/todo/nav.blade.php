<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" />

    <ul class="list">
        @if(in_array('list', $show))
            <a href="{{ route('todo.list') }}"><li>Back To List</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('todo.create') }}"><li class="top">Create New To-Do Item</li></a>
        @endif

        @if(in_array('create-from-habit', $show))
            <a href="{{ route('todo.create', ['from' => 'habit']) }}"><li>Create From Habit</li></a>
        @endif

        @if(in_array('create-from-goal', $show))
            <a href="{{ route('todo.create', ['from' => 'goal']) }}"><li>Create From Goal</li></a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-item-form" action="{{ route('todo.destroy', ['todo' => $item->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('todo.destroy', ['todo' => $item->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete To-Do Item?', '#delete-item-form')">
                <li>Delete Item</li>
            </a>
        @endif 
    </ul>
</nav>