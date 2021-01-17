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
    </div><br/><br/>

    {{-- Habit history tracker/toggle --}}
</div>