<div class="action-item">
    <form
        @if($action_item instanceof \App\Models\Bucketlist\BucketlistItem)
            action="{{ route('goals.toggle-achieved.bucketlist-item', ['bucketlist_item' => $action_item->uuid]) }}"
        @else
            action="{{ route('goals.toggle-achieved.action-item', ['action_item' => $action_item->uuid]) }}"
        @endif
        method="POST">
        @csrf
        <input class="submit-completed" name="achieved" type="checkbox" @if($action_item->achieved) checked @endif />
    </form>
    &nbsp;
    <a @if($action_item->achieved) class="achieved" @endif
        @if($action_item instanceof \App\Models\Bucketlist\BucketlistItem)
            href="{{ route('goals.view.bucketlist-item', ['bucketlist_item' => $action_item->uuid]) }}"
        @else
            href="{{ route('goals.view.action-item', ['action_item' => $action_item->uuid]) }}"
        @endif
        >{{ $action_item->name }}
    </a>
    &nbsp;
    <div class="deadline" title="Deadline: {{ $deadline }}"><p>{{ $deadline }}</p></div>
</div>