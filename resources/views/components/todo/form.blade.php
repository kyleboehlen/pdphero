<form class="to-do" action="{{ route($action) }}" method="POST">
    @csrf

    <h2>{{ $title }}</h2>

    <input type="text" name="title" placeholder="Title" @isset($item) value="{{ $item->title }}" @endisset/><br/><br/>

    @isset($item)
        <x-todo.priority-selector :selected="$item->priority" />
    @else
        <x-todo.priority-selector />
    @endisset

    <br/>

    <textarea name="notes" placeholder="Any notes for your to-do item go here!">@isset($item) value="{{ $item->notes }}" @endisset</textarea><br/><br/>

    <a href="{{ route('todo.list') }}">
        <button>Cancel</button>
    </a>

    <button type="submit">Submit</button>
</form>
    