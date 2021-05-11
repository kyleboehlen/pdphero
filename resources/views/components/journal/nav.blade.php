<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <img class="logo" src="{{ asset('logos/logo-white.png') }}" onclick="location.href='{{ route('home') }}'"/>

    <ul class="list">
        @if(in_array('back', $show))
            <a href="{{ route('journal') }}"><li>Back To List</li></a>
        @endif

        @if(in_array('back-day', $show))
            {{-- Gotta use day here somehow --}}
            <a href="{{ route('journal.view.day', ['day' => $idek]) }}"><li>Back To Day</li></a>
        @endif

        @if(in_array('back-entry', $show))
            <a href="{{ route('journal.view.entry', ['journal_entry' => $journal_entry->uuid]) }}"><li>Back To Entry</li></a>
        @endif

        @if(in_array('create', $show))
            <a href="{{ route('journal.create.entry') }}"><li>Add Entry</li></a>
        @endif

        @if(in_array('edit', $show))
            <a href="{{ route('journal.edit.entry', ['journal_entry' => $journal_entry->uuid]) }}"><li>Edit Entry</li></a>
        @endif

        @if(in_array('categories', $show))
            <a href="{{ route('journal.edit.categories') }}"><li>Edit Categories</li></a>
        @endif

        @if(in_array('search', $show))
            <a href="#"><li>Search Entries</li></a>
        @endif

        @if(in_array('color-key', $show))
            <a href="{{ route('journal.colors') }}"><li>Color Guide</li></a>
        @endif

        @if(in_array('delete', $show))
            <form id="delete-entry-form" class="verify-delete" action="{{ route('journal.destroy.entry', ['journal_entry' => $journal_entry->uuid]) }}" method="POST">
                @csrf
            </form>
            <a href="{{ route('journal.destroy.entry', ['journal_entry' => $journal_entry->uuid]) }}" class="destructive-option"
                onclick="event.preventDefault(); verifyDeleteForm('Delete Journal Entry?', '#delete-entry-form')">
                <li>Delete Entry</li>
            </a>
        @endif 
    </ul>
</nav>