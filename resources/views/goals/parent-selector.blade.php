@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="back-goal" :goal="$goal" />

    <div class="app-container">
        <form class="create-selector" action="{{ route('goals.convert-sub.submit', ['goal' => $goal->uuid]) }}" method="POST">
            @csrf
            
            <h2>Assign To Parent Goal</h2>

            <select name="parent-goal">
                @foreach($parent_goals as $parent_goal)
                    <option value="{{ $parent_goal->uuid }}">{{ $parent_goal->name }}</option>
                @endforeach
            </select>

            <button type="submit">Done</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection