<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('goals') }}"><li>Back To Goals</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('goals.create.goal') }}"><li>Create New Goal</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('goals.edit.goal', ['goal' => $goal->uuid]) }}"><li>Edit Goal</li></a>
        @endif

        @if(in_array('create-sub', $show))
            <a href="{{ route('goals.create.goal', ['parent-goal' => $goal->uuid]) }}"><li>Create Sub-Goal</li></a>
        @endif

        @if(in_array('shift', $show))
            <a href="{{ route('goals.shift-dates') }}"><li>Shift Dates</li></a>
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