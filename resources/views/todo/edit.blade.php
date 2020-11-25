@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav />

    <div class="app-container">
        @switch($item->type_id)
            @case($type::HABIT_ITEM)

                @break
            @case($type::ACTION_ITEM)
            
                @break
            @default
                <x-todo.form action="todo.update" :item="$item" title="Edit-Item"/>
        @endswitch
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection