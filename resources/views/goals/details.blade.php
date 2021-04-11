@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav :show="$nav_show" :goal="$goal" />

    <div class="app-container goal-details">
        {{-- Goal Category --}}
        <p class="goal-category">{{ !is_null($goal->category) ? $goal->category->name : 'Uncategorized' }}</p>

        {{-- Goal title --}}
        <h2>{{ $goal->name }}</h2>

        {{-- Nav dropdown --}}
        <select id="goal-nav-dropdown">
            @foreach($dropdown_nav as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select><br/>

        {{-- Goal Details --}}
        <div id="goal-details-div" class="goal-nav-div">
            {{-- Goal Image --}}
            <x-goals.image :goal="$goal" />

            {{-- Goal Reason --}}
            <br/>
            <h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reason&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>
            @foreach(explode(PHP_EOL, $goal->reason) as $line)
                <p>{{ $line }}</p>
            @endforeach

            {{-- Goal Notes --}}
            @if(!is_null($goal->notes))
                <h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>
                @foreach(explode(PHP_EOL, $goal->notes) as $line)
                    <p>{{ $line }}</p>
                @endforeach
            @endif
        </div>

        {{-- Goal Progress --}}
        <div id="goal-progress-div" class="goal-nav-div hidden">
            {{-- Goal progress bar --}}
            <x-app.progress-bar :percent="$goal->progress" /><br/>

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
        </div>
        
        {{-- Goal Action Plan --}}
        <div id="goal-action-plan-div" class="goal-nav-div hidden">
            action plan
        </div>

        {{-- Sub Goals --}}
        <div id="goal-sub-goals-div" class="goal-nav-div hidden">
            <br/>
            @foreach($goal->subgoals as $sub_goal)
                @if($goal->subgoals->count() < 3)
                    <x-goals.goal :goal="$sub_goal" class="align-start" />
                @else
                    <x-goals.goal :goal="$sub_goal" />
                @endif
            @endforeach
        </div>

        {{-- Ad Hoc List --}}
        <div id="goal-ad-hoc-list-div" class="goal-nav-div hidden">
            ad hoc list
        </div>

    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection