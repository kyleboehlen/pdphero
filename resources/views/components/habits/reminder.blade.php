<div class="reminder">
    @isset($reminder)
        <form class="reminder" action="{{ route('habits.destroy.reminder', ['reminder' => $reminder->uuid]) }}" method="POST">
            @csrf

            <p>{{ $reminder->remind_at_formatted }}</p>

            {{-- Trashcan icon/delete submit button --}}
            <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
            <button class="delete" type="submit">Delete</button>
        </form>
    @endisset

    @isset($habit)
        <form class="reminder" action="{{ route('habits.store.reminder', ['habit' => $habit->uuid]) }}" method="POST">
            @csrf
            <input type="time" name="time" required />
            <button class="add" type="submit">Add</button>
        </form>
    @endisset
</div>