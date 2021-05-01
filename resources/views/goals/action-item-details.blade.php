@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.action-item-nav :show="$show" :item="$action_item" />

    <div class="app-container action-item-details">
        {{-- Action item goal --}}
        <p class="goal-name">{{ $action_item->goal->name }}</p>

        {{-- Action item name --}}
        <h2>{{ $action_item->name }}</h2>

        {{-- Deadline --}}
        @if(!is_null($action_item->deadline))
            <p class="deadline">Due: {{ \Carbon\Carbon::parse($action_item->deadline)->format('n/j/y') }}</p>
        @else
            <p class="deadline"> <a class="deadline" id="set-deadline-link-{{ $action_item->uuid }}" href="#">Set Deadline</a></p>
            @push('scripts')
                <x-goals.ad-hoc-deadline-popup :item="$action_item" details="true" />
            @endpush
        @endif

        {{-- Notes --}}
        <h3 class="notes">Notes</h3>
        @if(!is_null($action_item->notes))
            @foreach(explode(PHP_EOL, $action_item->notes) as $line)
                <p class="notes">{{ $line }}</p>
            @endforeach
        @else
            <p class="notes">No notes for this action item!</p>
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection