<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />
    
    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(in_array('back', $show))
            @if(!is_null($month) && !is_null($year))
                <a href="{{ route('journal.view.list', ['month' => $month, 'year' => $year]) }}"><li>Back To List</li></a>
            @else
                <a href="{{ route('journal') }}"><li>Back To List</li></a>
            @endif
        @endif

        @if(in_array('back-day', $show))
            {{-- Gotta use day here somehow --}}
            <a href="{{ route('journal.view.day', ['date' => $date]) }}"><li>Back To Day</li></a>
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
            <a href="#" id="show-search-entries"><li>Search Entries</li></a>
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

{{-- Search popup --}}
@push('scripts')
    <div class="overlay-hide" id="search-entries-container">
        {{-- Search Headline --}}
        <h2>Search Journal Entries</h2><br/>
        
        {{-- Search Entries Form --}}
        <form action="{{ route('journal.search') }}" method="POST">
            @csrf

            <p>Search For:</p>
            <div class="search-for-container">
                <input type="text" name="keywords" placeholder="Keywords..." required/>
            </div>
            @error('keywords')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @enderror

            <div class="between-dates-container">
                <p>From:</p>
                <input type="date" name="start-date" required />
                <p>To:</p>
                <input type="date" name="end-date" required />
            </div>
            @error('start-date')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @enderror
            @error('end-date')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @enderror
            <br/><br/>

            {{-- Cancel/Save buttons --}}
            <div class="buttons-container">
                <button type="button" class="swal2-confirm swal2-styled journal-search-button overlay-cancel-button">Cancel</button>
                <button type="submit" class="swal2-confirm swal2-styled journal-search-button">Save</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function(){
            $('#show-search-entries').click(function(){
                $('.overlay').show();
                $('#search-entries-container').show();
            });
        });
    </script>
@endpush