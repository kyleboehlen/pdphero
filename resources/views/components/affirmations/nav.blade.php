<nav class="app">
    {{-- Close icon --}}
    <img id="close-nav" class="close hover-white" src="{{ asset('icons/close-black.png') }}" />

    {{-- Logo --}}
    <x-app.nav-logo />

    <ul class="list">
        @if(!in_array('back', $hide))
            <a href="{{ route('affirmations') }}"><li>Back To Affirmations</li></a>
        @endif

        @if(!in_array('create', $hide))
            <a href="{{ route('affirmations.create') }}"><li>Add Affirmation</li></a>
        @endif

        @isset($affirmation)
            @if(!in_array('edit', $hide))
                <a href="{{ route('affirmations.edit', ['affirmation' => $affirmation->uuid]) }}"><li>Edit Affirmation</li></a>
            @endif

            @if(!in_array('delete', $hide))
                <form id="delete-affirmation-form" action="{{ route('affirmations.destroy', ['affirmation' => $affirmation->uuid]) }}" method="POST">
                    @csrf
                </form>
                <a href="{{ route('affirmations.destroy', ['affirmation' => $affirmation->uuid]) }}" class="destructive-option"
                    onclick="event.preventDefault(); verifyDeleteForm('Delete Affirmation?', '#delete-affirmation-form')">
                    <li>Delete Affirmation</li>
                </a>
            @endif
        @endisset
    </ul>
</nav>