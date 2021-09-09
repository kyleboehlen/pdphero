<div class="category">
    @isset($category)
        <form class="category" action="{{ route('bucketlist.destroy.category', ['category' => $category->uuid]) }}" method="POST">
            @csrf

            <p>{{ $category->name }}</p>

            {{-- Trashcan icon/delete submit button --}}
            <img class="trash-can" src="{{ asset('icons/trashcan-white.png') }}" />
            <button class="delete" type="submit">Delete</button>
        </form>
    @else
        <form class="category" action="{{ route('bucketlist.store.category') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Add a new category" maxlength="255" />
            <button class="add" type="submit">Add</button>
        </form>
    @endif
</div>