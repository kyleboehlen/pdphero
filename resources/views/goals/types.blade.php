@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="back|create|categories" />

    <div class="app-container">
        @foreach(config('goals.types') as $goal_type)
            <div class="goal-type-container">
                <h2>{{ $goal_type['name'] }}</h2>
                <p>{{ $goal_type['desc'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection