<div class="rule">
    @isset($rule)
        <form class="rule" action="{{ route('profile.destroy.rule') }}" method="POST">
            @csrf

            {{-- Input/displayed value --}}
            <input type="hidden" name="rule" value="{{ $rule }}" />
            <p>{{ $rule }}</p>

            {{-- Trashcan icon/delete submit button --}}
            <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
            <button class="delete" type="submit">Delete</button>
        </form>
    @else
        <form class="rule" action="{{ route('profile.update.rules') }}" method="POST">
            @csrf
            <input type="text" name="rule" placeholder="Add a new rule :)" maxlength="255" />
            <button class="add" type="submit">Add</button>
        </form>
    @endif
</div>