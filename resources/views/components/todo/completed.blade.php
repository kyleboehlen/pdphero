<div class="completed">
    <h2>Completed Item</h2>

    <h3 class="title">Good job completing {{ $item->title }} at {{ $item->updated_at->format('g:i A') }} {{ $item->relativeUpdatedAt() }}!</h3>

    @if(!is_null($item->notes))
        <p>
            @foreach(explode(PHP_EOL, $item->notes) as $line)
                {{ $line }}<br/>
            @endforeach
        </p>
    @else
        <p>No notes for this one!</p>
    @endif

    <br/>

    <a href="{{ route('todo.list') }}">
        <button type="button">Okay</button>
    </a><br/><br/>
</form>