<div class="setting">
    <div class="anchor" id="anchor-{{ $settings_id }}"></div>
    <form action="{{ route('profile.update.settings', ['id' => $settings_id]) }}" method="POST">
        @csrf
        {{ $text_part_one }}
        <select class="options-setting" name="value">
            @foreach($options as $key => $option)
                <option value="{{ $key }}" @if($selected_option_key == $key) selected @endif>{{ $option }}</option>
            @endforeach
        </select>
        {{ $text_part_two }}
    </form>
</div>