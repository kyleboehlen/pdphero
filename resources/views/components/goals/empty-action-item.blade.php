<div class="action-item empty">
    <form action="{{ route('goals.create.action-item', ['goal' => $goal->uuid]) }}" method="GET">
        @csrf
        <input class="submit-completed" name="achieved" type="checkbox" />
    </form>
    &nbsp;
    <a href="{{ route('goals.create.action-item', ['goal' => $goal->uuid]) }}">
        Add an Action Item
    </a>
    &nbsp;
    <div class="deadline"><p>??/??/??</p></div>
</div>