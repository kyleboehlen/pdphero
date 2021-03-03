@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="create" />

    <div class="app-container">
        <div class="selector">
            <span>
                Show
                <select class="goal-selector" id="scope-selector">
                    @foreach ($scopes as $scope)
                        <option value="{{ $scope }}" @if($scope == $selected_scope) selected @endif>{{ ucwords($scope) }}</option>
                    @endforeach
                </select>
            </span>
            <span>
                Goals In 
                <select class="goal-selector" id="category-selector">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->uuid }}" @if(!is_null($selected_category) && $category->id == $selected_category->id) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
            </span>
        </div>

        @if($goals->count() == 0)
            <x-goals.empty-goal />
        @else
            @foreach($goals as $goal)
                <x-goals.goal :goal="$goal" />
            @endforeach
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection