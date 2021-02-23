@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    @switch($item->type_id)
        @case($type::RECURRING_HABIT_ITEM)
            <x-todo.nav show="list|create-from-habit|color-key" />
            <div class="app-container">
                <x-todo.form :item="$item" />
            </div>
            @break
        @case($type::SINGULAR_HABIT_ITEM)
            <x-todo.nav show="list|create-from-habit|color-key|delete" :item="$item" />
            <div class="app-container">
                <x-todo.form :item="$item" />
            </div>
            @break
        @case($type::ACTION_ITEM)
            {{-- Side Nav --}}
            <x-todo.nav show="list|create-from-goal|delete" />
            
            @break
        @default
            {{-- Side Nav --}}
            <x-todo.nav show="list|create|delete" :item="$item"/>

            <div class="app-container">
                <x-todo.form :item="$item"/>
            </div>
    @endswitch

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection