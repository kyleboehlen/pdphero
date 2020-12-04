<form class="to-do" @isset($item) action="{{ route($action, ['todo' => $item->uuid]) }}" @else action="{{ route($action) }}" @endisset method="POST">
    @csrf

    <h2>{{ $title }}</h2>

    <input type="text" name="title" placeholder="Title" maxlength="255" @isset($item) value="{{ $item->title }}" @else value="{{ old('title') }}" @endisset required />
    @error('title')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    @isset($item)
        <x-todo.priority-selector :selected="$item->priority->id" />
    @else
        <x-todo.priority-selector />
    @endisset

    <br/>

    <textarea name="notes" placeholder="Any notes for your to-do item go here!">@isset($item){{ $item->notes }}@else{{ old('notes') }}@endisset</textarea>
    @error('notes')
        <p class="error">{{ $message }}</p>
    @enderror

    <a href="{{ route('todo.list') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>
    