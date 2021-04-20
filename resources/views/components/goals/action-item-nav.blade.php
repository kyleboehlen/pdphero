<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('goals.view.goal', ['goal' => $goal->uuid]) }}"><li>Back To Goal</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('goals.edit.action-item', ['action_item' => $action_item->uuid]) }}"><li>Edit Action Item</li></a>
        @endif

        {{-- Todo: Mark complete/incomplete toggle --}}

        @if(in_array('delete', $show))
            <form id="delete-action-item-form" class="verify-delete" action="{{ route('goals.destroy.action-item', ['action_item' => $action_item->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.destroy.action_item', ['action_item' => $action_item->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete Action Item?', '#delete-action-item-form')">
                <li>Delete Action Item</li>
            </a>
        @endif 
    </ul>
</nav>