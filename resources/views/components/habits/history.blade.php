<div class="history-toggle-container">
    @foreach($habit_history_array as $day_key => $history)
        <div class="history-toggle-item">
            @foreach(explode('|', $history['label']) as $label)
                <p>{{ $label }}</p>
            @endforeach
            <div class="toggle-history {{ $history['classes'] }}"
                id="toggle-{{ $habit->uuid }}-{{ $day_key }}">
            </div>
        </div>

        {{-- Pop up history tracker script? --}}
        @push('scripts')
            <script>
                $(document).ready(function(){
                    $('#toggle-{{ $habit->uuid }}-{{ $history['label'] }}').click(function(){
                        // Custom HTML swwet alert?
                    });
                });
            </script>
        @endpush
    @endforeach
</div>