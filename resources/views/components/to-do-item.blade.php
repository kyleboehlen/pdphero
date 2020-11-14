<div class="to-do-item">
    <form action="{{ route('todo.toggle-completed', ['uuid' => $item->uuid]) }}" method="POST">
        <input type="checkbox" name="completed" @if($item->completed) checked @endif />
    </form>
    &nbsp;
    <a @if($item->completed) class="completed" @endif href="{{ route('todo.edit', ['uuid' => $item->uuid]) }}">
        {{ $item->title }}
    </a>
    &nbsp;
    <div class="priority {{ strtolower($item->priority->name) }}"></div>
</div>