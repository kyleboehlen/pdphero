<div class="value">
    @isset($value)
        <form class="value" action="{{ route('profile.delete.value') }}" method="POST">
            @csrf

            {{-- Input/displayed value --}}
            <input type="hidden" value="{{ $value }}" />
            <p>{{ $value }}</p>

            {{-- Trashcan icon/delete submit button --}}
            <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
            <button class="delete" type="submit">Delete</button>
        </form>
    @else
        <form class="value" action="{{ route('profile.update.values') }}" method="POST">
            @csrf
            <input type="text" name="value" placeholder="Add a new value :)" maxlength="255" />
            <button class="add" type="submit">Add</button>
        </form>
    @endif
</div>