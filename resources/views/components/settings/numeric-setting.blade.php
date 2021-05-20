<div class="setting">
    <div class="anchor" id="anchor-{{ $settings_id }}"></div>
    <form action="{{ route('profile.update.settings', ['id' => $settings_id]) }}" method="POST">
        @csrf
        {{ $text_part_one }}
        <input class="numeric-setting" type="number" name="value" min="{{ $min }}" max="{{ $max }}" value="{{ $current_value }}" required />
        {{ $text_part_two }}
        @error('hours')
            @push('scripts')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @endpush
        @enderror
    </form>
</div>