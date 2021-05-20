<div class="action-item">
    <form action="{{ route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid]) }}" method="POST">
        @csrf
        <input class="submit-completed" name="achieved" type="checkbox" @if($action_item->achieved) checked @endif />
    </form>
    &nbsp;
    <a @if($action_item->achieved) class="achieved" @endif href="{{ route('goals.view.action-item', ['action_item' => $action_item->uuid]) }}">
        {{ $action_item->name }}
    </a>
    &nbsp;
    <div class="deadline" title="Deadline: {{ $deadline }}"><p>{{ $deadline }}</p></div>
</div>