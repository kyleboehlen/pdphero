<div class="to-do-item">
    <form action="{{ route('todo.toggle-completed', ['todo' => $item->uuid]) }}" method="POST">
        @csrf
        <input class="submit-completed" name="completed" type="checkbox" @if($item->completed) checked @endif />
    </form>
    &nbsp;
    <a @if($item->completed) class="completed" @endif href="{{ route('todo.edit', ['todo' => $item->uuid]) }}">
        {{ $item->title }}
    </a>
    &nbsp;
    <div class="priority {{ strtolower($item->priority->name) }}" title="Priority: {{ $item->priority->name }}"></div>
</div>