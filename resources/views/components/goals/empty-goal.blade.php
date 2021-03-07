<div class="goal empty @if($scope == 'achieved') achieved-padding @endif">
    {{-- Goal image --}}
    <img class="goal-img" src="{{ asset('icons/plus-white.png') }}" />

    {{-- Goal title/link --}}
    @if($scope == 'future')
        <a href="{{ route('goals.create.goal', ['type' => $type::FUTURE_GOAL]) }}">
            <h2>Create A Future Goal</h2>
        </a>
    @else
        <a href="{{ route('goals.create.goal') }}">
            <h2>Create A New Goal</h2>
        </a>
    @endif

    @if($scope != 'future')
        {{-- Goal progress bar --}}
        <x-app.progress-bar percent="0" /><br/>

        @if($scope != 'achieved')
            {{-- Status label/bar --}}
            <div class="status" title="This goal needs to be created!">
                <p class="label tbd">To Be Determined</p>
                <img src="{{ asset('icons/goal-status-bar-tbd.png') }}" />
            </div>
        @endif
    @endif
</div>