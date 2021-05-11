<div class="mood-container">
    @foreach($moods as $id => $mood)
        <input class="mood-checkbox {{ strtolower($mood['name']) }}" name="mood-{{ $id }}" type="checkbox" @if($id == $selected || old("mood-$id") == 'on') checked @endif />
    @endforeach
</div>