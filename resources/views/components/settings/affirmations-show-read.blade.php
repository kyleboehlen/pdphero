<div class="setting">
    <div class="anchor" id="anchor-{{ $settings_id }}"></div>
    <form action="{{ route('profile.update.settings', ['id' => $settings_id]) }}" method="POST">
        @csrf
        <input class="submit-completed" type="checkbox" name="show" @if($checked) checked @endif />Display the 'Good Job!' page after finishing reading affirmations
    </form>
</div>