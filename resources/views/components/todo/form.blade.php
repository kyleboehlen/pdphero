<form class="to-do" method="POST"
    @isset($item)
        @switch($item->type_id)
            @case($type::RECURRING_HABIT_ITEM)
            @case($type::SINGULAR_HABIT_ITEM)
                action="{{ route('todo.update.habit', ['todo' => $item->uuid]) }}"
                @break
            @default
                action="{{ route('todo.update', ['todo' => $item->uuid]) }}"
        @endswitch
    @else
        @switch($create_type)
            @case($type::SINGULAR_HABIT_ITEM)
                action="{{ route('todo.store.habit') }}"
                @break
            @default
                action="{{ route('todo.store') }}"
        @endswitch
    @endisset>
    @csrf

    {{-- Header --}}
    <h2>@isset($item) Edit Item @else Create New Item @endisset</h2>

    @if(is_null($item) && $create_type == $type::SINGULAR_HABIT_ITEM)
        <select name="habit">
            @foreach($habits as $habit)
                <option value="{{ $habit->uuid }}">{{ $habit->name }}</option>
            @endforeach
        </select>
        @error('habit')
            <p class="error">{{ $message }}</p>
        @enderror
    @else
        <input type="text" name="title" placeholder="Title" maxlength="255" 
            @isset($item)
                value="{{ $item->title }}"
                @if($item->type_id == $type::RECURRING_HABIT_ITEM || $item->type_id == $type::SINGULAR_HABIT_ITEM)
                    disabled
                @endif
            @else
                value="{{ old('title') }}" 
            @endisset required />
        @error('title')
            <p class="error">{{ $message }}</p>
        @enderror
    @endif
    <br/><br/>

    @isset($item)
        <x-todo.priority-selector :selected="$item->priority->id" />
    @else
        <x-todo.priority-selector />
    @endisset

    <br/>
    <textarea name="notes"
        @if(is_null($item) && $create_type == $type::SINGULAR_HABIT_ITEM)
            placeholder="If left blank the habit's notes will be used."
        @else
            placeholder="Any notes for your to-do item go here!"
        @endif
    >@isset($item){{ $item->notes }}@else{{ old('notes') }}@endisset</textarea>
    @error('notes')
        <p class="error">{{ $message }}</p>
    @enderror

    <a href="{{ route('todo.list') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>
    