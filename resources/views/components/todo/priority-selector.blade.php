<div class="priority-container">
    @foreach($priorities as $id => $priority)
        <input class="priority-checkbox {{ strtolower($priority['name']) }}" name="priority-{{ $id }}" type="checkbox" @if($id == $selected) checked @endif />
    @endforeach
</div>