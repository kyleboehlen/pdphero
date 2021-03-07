@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="categories" />

    <div class="app-container">
        <form class="create-selector" action="{{ route('goals.create.goal') }}" method="GET">
            
            {{-- Hidden fields --}}
            @isset($future_goal_uuid)
                <input type="hidden" name="future-goal" value="{{ $future_goal_uuid }}" />
            @endisset

            @isset($parent_goal_uuid)
                <input type="hidden" name="parent-goal" value="{{ $parent_goal_uuid }}" />
            @endisset

            <h2>Select a Goal Type</h2>

            <select id="goal-create-type-select" name="type">
                @foreach($goal_types as $goal_type)
                    <option value="{{ $goal_type->id }}">{{ $goal_type->name }}</option>
                @endforeach
            </select>

            @foreach($goal_types as $goal_type)
                <p id="goal-create-type-description-{{ $goal_type->id }}" class="goal-create-type-description">{{ $goal_type->desc }}</p>
            @endforeach

            <button type="submit">Next</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection