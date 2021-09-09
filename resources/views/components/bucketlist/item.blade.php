<div class="bucketlist-item filter-category {{ is_null($item->category) ? '' : 'category-' . $item->category->uuid }}">
    <form action="{{ route('bucketlist.mark-completed', ['bucketlist_item' => $item->uuid]) }}" method="POST">
        @csrf
        <input class="submit-completed" name="completed" type="checkbox" />
    </form>
    &nbsp;
    <a href="{{ route('bucketlist.view.details', ['bucketlist_item' => $item->uuid]) }}">
        {{ $item->name }}
    </a>
</div>