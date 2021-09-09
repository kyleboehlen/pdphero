<div class="bucketlist-item empty">
    <form action="{{ route('bucketlist.create') }}" method="GET">
        <input class="submit-completed" name="completed" type="checkbox" />
    </form>
    &nbsp;
    <a href="{{ route('bucketlist.create') }}">
        Create a new Bucketlist Item
    </a>
</div>