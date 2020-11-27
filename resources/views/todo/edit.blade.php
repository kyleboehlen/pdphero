@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    @switch($item->type_id)
        @case($type::HABIT_ITEM)
            {{-- Side Nav --}}
            <x-todo.nav show="list|create-from-habit|delete" />

            @break
        @case($type::ACTION_ITEM)
            {{-- Side Nav --}}
            <x-todo.nav show="list|create-from-goal|delete" />
            
            @break
        @default
            {{-- Side Nav --}}
            <x-todo.nav show="list|create|delete" :item="$item"/>

            <div class="app-container">
                <x-todo.form action="todo.update" :item="$item" title="Edit-Item"/>
            </div>
    @endswitch

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection