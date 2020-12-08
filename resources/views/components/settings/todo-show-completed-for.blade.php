<div class="setting">
    <div class="anchor" id="anchor-{{ $settings_id }}"></div>
    <form action="{{ route('profile.update.settings', ['id' => $settings_id]) }}" method="POST">
        @csrf
        Show completed items on the list for <input class="text-setting" type="number" name="hours" min="0" max="100" value="{{ $hours }}" required /> hours
        @error('hours')
            @push('pop-up-boxes')
                <x-app.pop-up-box title="Error" :message="$message" />
            @endpush
        @enderror
    </form>
</div>