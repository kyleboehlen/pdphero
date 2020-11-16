<div class="to-do-item empty">
    <form action="{{ route('todo.create') }}" method="GET">
        <input class="submit-completed" name="completed" type="checkbox" />
    </form>
    &nbsp;
    <a href="{{ route('todo.create') }}">
        Create a new To-Do Item
    </a>
    &nbsp;
    <div class="priority"></div>
</div>