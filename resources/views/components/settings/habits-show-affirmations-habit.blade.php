<div class="setting">
    <div class="anchor" id="anchor-{{ $settings_id }}"></div>
    <form action="{{ route('profile.update.settings', ['id' => $settings_id]) }}" method="POST">
        @csrf
        <input class="submit-completed" type="checkbox" name="value" @if($checked) checked @endif />Show the affirmations habit
    </form>
</div>