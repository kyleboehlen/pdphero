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