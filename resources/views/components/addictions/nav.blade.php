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
            <a href="{{ route('addiction.details', ['addiction' => $addiction->uuid]) }}"><li>Back To Details</li></a>
        @endif        
        
        @if(in_array('edit', $show))
            <a href="{{ route('addiction.edit', ['addiction' => $addiction->uuid]) }}"><li>Edit Addiction</li></a>
        @endif

        @if(in_array('milestones', $show))
            <a href="{{ route('addiction.milestones', ['addiction' => $addiction->uuid]) }}"><li>Edit Milestones</li></a>
        @endif

        @if(in_array('relapse-timeline', $show))
            <a href="{{ route('addiction.relapse.timeline', ['addiction' => $addiction->uuid]) }}"><li>View Relapses</li></a>
        @endif

        @if(in_array('moderate', $show))
            <form id="addiction-usage-form" action="{{ route('addiction.usage.store', ['addiction' => $addiction->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('addiction.usage.store', ['addiction' => $addiction->uuid]) }}"
                onclick="event.preventDefault(); verifyUsageForm('Mark Moderated Usage?', '#addiction-usage-form')">
                <li>Mark Usage</li>
            </a>
        @endif

        @if(in_array('relapse-form', $show))
            <a href="{{ route('addiction.relapse.create', ['addiction' => $addiction->uuid]) }}"><li>Mark Relapse</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('addiction.create') }}"><li>Create New Addiction</li></a>
        @endif

        @if(in_array('create-milestone', $show))
            <a href="{{ route('addiction.milestone.create', ['addiction' => $addiction->uuid]) }}"><li>Create New Milestone</li></a>
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