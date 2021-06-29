@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="back-goal" :goal="$goal" />

    <div class="app-container">
        <form class="create-selector" action="{{ route('goals.transfer-ad-hoc-items.submit', ['goal' => $goal->uuid]) }}" method="POST">
            @csrf
            
            <h2>Transfer Ad-Hoc Items to New Goal</h2>

            <select name="ad-hoc-goal">
                @foreach($ad_hoc_goals as $ad_hoc_goal)
                    <option value="{{ $ad_hoc_goal->uuid }}">{{ $ad_hoc_goal->name }}</option>
                @endforeach
            </select>

            <button type="submit">Done</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection