<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>

    <ul class="list">
        @if(in_array('back-item', $show))
            <a href="{{ route('goals.view.action-item', ['action_item' => $action_item->uuid]) }}"><li>Back To Action Item</li></a>
        @endif
        
        @if(in_array('back-goal', $show))
            <a href="{{ route('goals.view.goal', ['goal' => $goal->uuid, 'selected-dropdown' => $selected_dropdown]) }}"><li>Back To Goal</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('goals.edit.action-item', ['action_item' => $action_item->uuid]) }}"><li>Edit Action Item</li></a>
        @endif

        @if(in_array('reminders', $show))
            <a href="{{ route('goals.edit.reminders', ['action_item' => $action_item->uuid]) }}"><li>Edit Reminders</li></a>
        @endif

        @if(in_array('clear-deadline', $show))
            <form id="clear-deadline-form" action="{{ route('goals.ad-hoc-deadline.clear', ['action_item' => $action_item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.ad-hoc-deadline.clear', ['action_item' => $action_item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('clear-deadline-form').submit();">
                <li>Clear Deadline</li>
            </a>
        @endif

        @if(in_array('toggle-achieved', $show))
            <form id="toggle-achieved-item-form" action="{{ route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('toggle-achieved-item-form').submit();">
                <li>Mark Achieved</li>
            </a>
        @endif

        @if(in_array('toggle-unachieved', $show))
            <form id="toggle-unachieved-item-form" action="{{ route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid, 'view_details' => true]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('toggle-unachieved-item-form').submit();">
                <li>Mark Unachieved</li>
            </a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-action-item-form" class="verify-delete" action="{{ route('goals.destroy.action-item', ['action_item' => $action_item->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.destroy.action-item', ['action_item' => $action_item->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete Action Item?', '#delete-action-item-form')">
                <li>Delete Action Item</li>
            </a>
        @endif 
    </ul>
</nav>