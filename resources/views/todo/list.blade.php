@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav show="create|create-from-habit|create-from-goal|color-key" />

    <div class="app-container">
        @if($to_do_items->count() == 0)
            <x-todo.empty-list-item />
        @else
            @foreach($to_do_items as $item)
                <x-todo.item :item="$item" />
            @endforeach
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection