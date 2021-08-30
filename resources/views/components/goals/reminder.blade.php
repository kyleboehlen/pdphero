<div class="reminder">
    @isset($reminder)
        <form class="reminder" action="{{ route('goals.destroy.reminder', ['reminder' => $reminder->uuid]) }}" method="POST">
            @csrf

            <p>{{ $reminder->remind_at_formatted }}</p>

            {{-- Trashcan icon/delete submit button --}}
            <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
            <button class="delete" type="submit">Delete</button>
        </form>
    @endisset

    @isset($action_item)
        <form class="reminder" action="{{ route('goals.store.reminder', ['action_item' => $action_item->uuid]) }}" method="POST">
            @csrf
            <input type="date" name="date" required />
            <input type="time" name="time" required />
            <button class="add" type="submit">Add</button>
        </form>
    @endisset
</div>