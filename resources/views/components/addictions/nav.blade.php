<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(in_array('list', $show))
            <a href="{{ route('addictions') }}"><li>Back To List</li></a>
        @endif

        @if(in_array('back', $show))
            <a href="{{ route('addictions.view.details', ['addiction' => $addiction->uuid]) }}"><li>Back To Details</li></a>
        @endif

        {{-- TODO: add relapse option - maybe change the wording based on method/num times used --}}
        
        @if(in_array('edit', $show))
            <a href="{{ route('addiction.edit', ['addiction' => $addiction->uuid]) }}"><li>Edit Addiction</li></a>
        @endif

        @if(in_array('milestones', $show))
            <a href="{{ route('addictions.edit.milestones', ['addiction' => $addiction->uuid]) }}"><li>Edit Milestones</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('addiction.create') }}"><li>Create New Addiction</li></a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-addiction-form" action="{{ route('addiction.destroy', ['addiction' => $addiction->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('addiction.destroy', ['addiction' => $addiction->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete Addiction?', '#delete-addiction-form')">
                <li>Delete Addiction</li>
            </a>
        @endif 
    </ul>
</nav>