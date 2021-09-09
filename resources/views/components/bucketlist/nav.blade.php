<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('bucketlist.view.details', ['bucketlist_item' => $item->uuid]) }}"><li>Back To Item</li></a>
        @endif

        @if(in_array('list', $show))
            <a href="{{ route('bucketlist') }}"><li>Back To List</li></a>
        @endif

        @if(in_array('mark-completed', $show))
            <form id="mark-completed-item-form" action="{{ route('bucketlist.mark-completed', ['bucketlist_item' => $item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('bucketlist.mark-completed', ['bucketlist_item' => $item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('mark-completed-item-form').submit();">
                <li>Mark Completed</li>
            </a>
        @endif

        @if(in_array('mark-incomplete', $show))
            <form id="mark-incomplete-item-form" action="{{ route('bucketlist.mark-incomplete', ['bucketlist_item' => $item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('bucketlist.mark-incomplete', ['bucketlist_item' => $item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('mark-incomplete-item-form').submit();">
                <li>Mark Incomplete</li>
            </a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('bucketlist.edit', ['bucketlist_item' => $item->uuid]) }}"><li>Edit Item</li></a>
        @endif

        @if(in_array('completed', $show))
            <a href="{{ route('bucketlist.view.completed') }}"><li>View Completed Items</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('bucketlist.create') }}"><li>Create New Item</li></a>
        @endif

        @if(in_array('edit-categories', $show))
            <a href="{{ route('bucketlist.edit.categories') }}"><li>Edit Categories</li></a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-item-form" action="{{ route('bucketlist.destroy', ['bucketlist_item' => $item->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('bucketlist.destroy', ['bucketlist_item' => $item->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete Item?', '#delete-item-form')">
                <li>Delete Item</li>
            </a>
        @endif 
    </ul>
</nav>