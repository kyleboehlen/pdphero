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

    {{-- Forgive me father for I have sinned, if sms_verified is set then we already checked it's NOTIFICATION_CHANNEL so.. it works, okay? --}}
    @isset($sms_verified)
        {{-- Webpush button --}}
        @if($selected_option_key == 'webpush')
            <span class="action-needed request-webpush">Please <a href="#" id="request-webpush">allow push notifications</a></span>
        @endif

        {{-- Confirm SMS button --}}
        @if($selected_option_key == 'sms' && !$sms_verified)
            <span class="action-needed">Please <a href="{{ route('profile.sms.edit') }}">verify your SMS number</a> to use SMS notifications</span>
        @endif
    @endisset
</div>