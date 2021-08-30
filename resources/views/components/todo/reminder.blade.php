<div class="reminder">
    @isset($reminder)
        <form class="reminder" action="{{ route('todo.destroy.reminder', ['reminder' => $reminder->uuid]) }}" method="POST">
            @csrf

            <p>{{ $reminder->remind_at_formatted }}</p>

            {{-- Trashcan icon/delete submit button --}}
            <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
            <button class="delete" type="submit">Delete</button>
        </form>
    @endisset

    @isset($todo)
        <form class="reminder" action="{{ route('todo.store.reminder', ['todo' => $todo->uuid]) }}" method="POST">
            @csrf
            <input type="date" name="date" required />
            <input type="time" name="time" required />
            <button class="add" type="submit">Add</button>
        </form>
    @endisset
</div>