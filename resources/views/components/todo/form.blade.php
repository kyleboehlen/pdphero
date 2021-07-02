<form class="to-do" method="POST"
    @isset($item)
        @switch($item->type_id)
            @case($type::RECURRING_HABIT_ITEM)
            @case($type::SINGULAR_HABIT_ITEM)
            @case($type::JOURNAL_HABIT_ITEM)
            @case($type::AFFIRMATIONS_HABIT_ITEM)
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
                @if(in_array($item->type_id, [$type::RECURRING_HABIT_ITEM, $type::SINGULAR_HABIT_ITEM, $type::ACTION_ITEM, $type::JOURNAL_HABIT_ITEM, $type::AFFIRMATIONS_HABIT_ITEM]))
                    disabled
                @endif
            @else
                value="{{ old('title') }}" 
            @endisset required />

        @if(isset($item) && $item->type_id == $type::ACTION_ITEM)
            <input type="hidden" name="title" value="{{ $item->title }}" />
        @endif

        @error('title')
            <p class="error">{{ $message }}</p>
        @enderror
    @endif
    <br/><br/>

    <select name="category" required>
        <option @if(is_null($item) && is_null(old('category'))) selected @endif value="no-category">No Category</option>

        @foreach($categories as $category)
            <option value="{{ $category->uuid }}"
                @if(!is_null($item) && $item->category_id == $category->id)
                    selected
                @elseif(old('category') == $category->uuid)
                    selected
                @endif
            >{{ $category->name }}</option>
        @endforeach
    </select>
    @error('category')
        <p class="error">{{ $message }}</p>
    @enderror

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
    