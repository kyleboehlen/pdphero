@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="create|categories|search|color-key" />

    <div class="app-container">
        <div class="selector">
            {{-- <span>
                <select class="goal-selector" id="scope-selector">
                    @foreach ($scopes as $scope)
                        <option value="{{ $scope }}" @if($scope == $selected_scope) selected @endif>{{ ucwords($scope) }}</option>
                    @endforeach
                </select>
            </span>
            <span>
                <select class="goal-selector" id="category-selector">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->uuid }}" @if(!is_null($selected_category) && $category->id == $selected_category->id) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
            </span> --}}
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection