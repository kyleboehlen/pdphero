<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('goals', ['scope' => $scope]) }}"><li>Back To Goals</li></a>
        @endif

        @if(in_array('back-goal', $show))
            <a href="{{ route('goals.view.goal', ['goal' => $goal->uuid]) }}"><li>Back To Goal</li></a>
        @endif

        @if(in_array('parent-back', $show))
            <a href="{{ route('goals.view.goal', ['goal' => $goal->parent->uuid, 'selected-dropdown' => 'sub-goals']) }}"><li>Back To Parent</li></a>
        @endif

        @if(in_array('remove-parent', $show))
            <form id="remove-parent-form" action="{{ route('goals.remove-parent', ['goal' => $goal->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.remove-parent', ['goal' => $goal->uuid]) }}"
                onclick="event.preventDefault(); verifyRemoveForm('Remove From Parent?', '#remove-parent-form')">
                <li>Remove From Parent</li>
            </a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('goals.create.goal') }}"><li>Create New Goal</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('goals.edit.goal', ['goal' => $goal->uuid]) }}"><li>Edit Goal</li></a>
        @endif

        @if(in_array('convert-sub', $show))
            <a href="{{ route('goals.convert-sub.form', ['goal' => $goal->uuid]) }}"><li>Convert to Sub-Goal</li></a>
        @endif

        @if(in_array('convert-active', $show))
            <a href="{{ route('goals.create.goal', ['future-goal' => $goal->uuid]) }}"><li>Convert To Active</li></a>
        @endif

        @if(in_array('create-sub', $show))
            <a href="{{ route('goals.create.goal', ['parent-goal' => $goal->uuid]) }}"><li>Create Sub-Goal</li></a>
        @endif

        @if(in_array('create-action-item', $show))
            <a href="{{ route('goals.create.action-item', ['goal' => $goal->uuid]) }}"><li>Add Action Item</li></a>
        @endif

        @if(in_array('transfer-ad-hoc-items', $show))
            <a href="{{ route('goals.transfer-ad-hoc-items.form', ['goal' => $goal->uuid]) }}"><li>Move Ad Hoc Items</li></a>
        @endif

        @if(in_array('shift-dates', $show))
            <a id="show-shift-dates" href="#"><li>Shift Dates</li></a>
        @endif

        @if(in_array('update-manual-progress', $show))
            <a id="show-manual-progress" href="#"><li>Update Progress</li></a>
        @endif

        @if(in_array('toggle-achieved', $show))
            <form id="toggle-achieved-form" action="{{ route('goals.toggle-achieved.goal', ['goal' => $goal->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.toggle-achieved.goal', ['goal' => $goal->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('toggle-achieved-form').submit();">
                <li>Mark Achieved</li>
            </a>
        @endif

        @if(in_array('toggle-unachieved', $show))
            <form id="toggle-unachieved-form" action="{{ route('goals.toggle-achieved.goal', ['goal' => $goal->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.toggle-achieved.goal', ['goal' => $goal->uuid, 'view_details' => true]) }}"
                onclick="event.preventDefault(); document.getElementById('toggle-unachieved-form').submit();">
                <li>Mark Unachieved</li>
            </a>
        @endif

        @if(in_array('categories', $show))
            <a href="{{ route('goals.edit.categories') }}"><li>Edit Categories</li></a>
        @endif

        @if(in_array('types', $show))
            <a href="{{ route('goals.types') }}"><li>Goal Types</li></a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-goal-form" class="verify-delete" action="{{ route('goals.destroy.goal', ['goal' => $goal->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('goals.destroy.goal', ['goal' => $goal->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete Goal?', '#delete-goal-form')">
                <li>Delete Goal</li>
            </a>
        @endif 
    </ul>
</nav>