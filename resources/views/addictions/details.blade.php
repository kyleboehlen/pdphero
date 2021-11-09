@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="list|edit|relapse|milestones|delete" :addiction="$addiction" />

    <div class="app-container details">
        {{-- Method --}}
        <p class="method">{{ config('addictions.methods')[$addiction->method_id]['name'] }}</p>

        {{-- Name --}}
        <h2>{{ $addiction->name }}</h2>

        {{-- Elapsed js timer --}}
        <x-addictions.elapsed-timer :addiction="$addiction" />

        {{-- Usage/milestone --}}
        @if($addiction->method_id == $method::ABSTINENCE)
            <p class="milestone {{ is_null($milestone_name) ? 'yellow' : 'green' }}">{{ $milestone_name ?? 'Pending Milestone'}}</p>
        @elseif($addiction->method_id == $method::MODERATION)
            <p class="usage"><span class="color {{ $usage_color }}"> {{ $usage }}</span> / {{ $addiction->moderated_amount }}</p>
        @endif

        {{-- Details --}}
        <h3>Details</h3>
        @foreach(explode(PHP_EOL, $addiction->details) as $line)
            <p class="details">{{ $line }}</p>
        @endforeach
        <br/>

        {{-- Milestone list --}}
        <h3>Milestones</h3>
        <span class="milestones-timeline">
            @foreach($addiction->milestones as $milestone)
                <span class="time-spacer"></span>
                <span class="time-label">{{ $milestone->carbon_reached->format('m/d/y') }}</span>
                <div class="milestone-timeline {{ $milestone->reached ? '' : 'unreached' }}">
                    <p>
                        {{ $milestone->name }}@if(!is_null($milestone->reward)): {{ $milestone->reward }}@endif
                    </p>
                    <div class="reached {{ $milestone->reached ? 'green' : 'red' }}"></div>
                </div>
            @endforeach
        </span>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection