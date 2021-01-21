<div class="habit">
    {{-- Habit title/link --}}
    <a href="{{ route('habits.view', ['habit' => $habit->uuid]) }}">
        <h2>{{ $habit->name }}</h2>
    </a>

    {{-- Habit progress bar --}}
    <div class="progress-container">
        <div class="progress-bar {{ $habit->strength == 0 ? 'no-border' : '' }}"
            style="background-color:{{ $habit->getRGB() }};padding-left: {{ $habit->getPadding() }}%;width:{{ $habit->strength == 0 ? 100 : $habit->strength }}%">
                <span @if($habit->isLowPercentage()) class="low-percentage" @endif>
                    {{ $habit->strength }}%
                </span>
        </div>
    </div><br/>

    {{-- Habit history tracker/toggle --}}
    <div class="history-toggle-container">
        @foreach($habit->getHistoryArray() as $day_key => $history)
            <div class="history-toggle-item">
                <p>{{ $history['label'] }}</p>
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
</div>