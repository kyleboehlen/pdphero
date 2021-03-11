<div class="goal {{ $class }} @if($scope == 'achieved') achieved-padding @endif">
    {{-- Goal image --}}
    @if($goal->use_custom_img)
        <img class="goal-img" src="{{ asset("goal-images/$goal->uuid.png") }}" />
    @else
        @if($scope == 'future')
            <img class="goal-img" src="{{ asset('icons/goals-white.png') }}" />
        @else
            @switch($goal->status_id)
                @case($status::TBD)
                    <img class="goal-img" src="{{ asset('icons/goal-status-tbd-black.png') }}" />
                    @break
                @case($status::LAGGING)
                    <img class="goal-img" src="{{ asset('icons/goal-status-lagging-black.png') }}" />
                    @break
                @case($status::ON_TRACK)
                    <img class="goal-img" src="{{ asset('icons/goal-status-ontrack-black.png') }}" />
                    @break
                @case($status::AHEAD)
                    <img class="goal-img" src="{{ asset('icons/goal-status-ahead-black.png') }}" />
                    @break
                @case($status::COMPLETED)
                    <img class="goal-img" src="{{ asset('icons/goal-status-completed-black.png') }}" />
                    @break
            @endswitch
        @endif
    @endif

    {{-- Goal title/link --}}
    <a href="{{ route('goals.view.goal', ['goal' => $goal->uuid]) }}">
        <h2>{{ $goal->name }}</h2>
    </a>

    @if($scope != 'future')
        {{-- Goal progress bar --}}
        <x-app.progress-bar :percent="$goal->progress" /><br/>

        @if($scope != 'achieved')
            {{-- Status label/bar --}}
            <div class="status" title="{{ $goal->status->desc }}">
                <p class="label {{ $goal->status->class }}">{{ $goal->status->name }}</p>
                @switch($goal->status_id)
                    @case($status::TBD)
                        <img src="{{ asset('icons/goal-status-bar-tbd.png') }}" />
                        @break
                    @case($status::LAGGING)
                        <img src="{{ asset('icons/goal-status-bar-lagging.png') }}" />
                        @break
                    @case($status::ON_TRACK)
                        <img src="{{ asset('icons/goal-status-bar-ontrack.png') }}" />
                        @break
                    @case($status::AHEAD)
                        <img src="{{ asset('icons/goal-status-bar-ahead.png') }}" />
                        @break
                    @case($status::COMPLETED)
                        <img src="{{ asset('icons/goal-status-bar-completed.png') }}" />
                        @break
                @endswitch
            </div>
        @endif
    @endif
</div>