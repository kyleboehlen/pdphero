<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(in_array('list', $show))
            <a href="{{ route('todo.list') }}"><li>Back To List</li></a>
        @endif

        @if(in_array('toggle-complete', $show))
            <form id="toggle-complete-item-form" action="{{ route('todo.toggle-completed', ['todo' => $item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('todo.toggle-completed', ['todo' => $item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('toggle-complete-item-form').submit();">
                <li>Mark Completed</li>
            </a>
        @endif

        @if(in_array('toggle-incomplete', $show))
            <form id="toggle-incomplete-item-form" action="{{ route('todo.toggle-completed', ['todo' => $item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('todo.toggle-completed', ['todo' => $item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('toggle-incomplete-item-form').submit();">
                <li>Mark Incomplete</li>
            </a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('todo.edit', ['todo' => $item->uuid]) }}"><li>Edit To-Do Item</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('todo.create') }}"><li class="top">Create New To-Do Item</li></a>
        @endif

        @if(in_array('create-from-habit', $show))
            <a href="{{ route('todo.create.habit') }}"><li>Create From Habit</li></a>
        @endif

        @if(in_array('color-key', $show))
            <a href="{{ route('todo.colors') }}"><li>Color Guide</li></a>
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