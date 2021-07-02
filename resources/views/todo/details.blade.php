@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    @switch($item->type_id)
        @case($type::RECURRING_HABIT_ITEM)
            <x-todo.nav show="list|toggle-complete|move-to-top|create-from-habit|color-key|edit" :item="$item" />
            @break
        @case($type::SINGULAR_HABIT_ITEM)
            <x-todo.nav show="list|toggle-complete|move-to-top|create-from-habit|color-key|edit|delete" :item="$item" />
            @break
        @case($type::ACTION_ITEM)
            <x-todo.nav show="list|toggle-complete|move-to-top|color-key|edit" :item="$item" />
            @break
        @case($type::JOURNAL_HABIT_ITEM)
            <x-todo.nav show="list|create-journal-entry|move-to-top|create-from-habit|color-key|edit" :item="$item" />
            @break
        @case($type::AFFIRMATIONS_HABIT_ITEM)
            <x-todo.nav show="list|read-affirmations|move-to-top|create-from-habit|color-key|edit" :item="$item" />
            @break
        @default
            <x-todo.nav show="list|toggle-complete|move-to-top|create|edit|delete" :item="$item"/>
    @endswitch

    <div class="app-container">
        <div class="todo-details">
            {{-- Category --}}
            @if(!is_null($item->category))
                <p class="todo-category">{{ $item->category->name }}</p>
            @else
                <br/><br/>
            @endif

            {{-- Header name --}}
            <h2>{{ $item->title }}</h2>

            {{-- Priority --}}
            <h3 class="priority {{ strtolower(config('todo.priorities')[$item->priority_id]['name']) }}">{{ config('todo.priorities')[$item->priority_id]['name'] }} Priority</h3>

            {{-- Notes --}}
            @if(isset($item->notes))
                <div class="notes-container">
                    <p>
                        @foreach(explode(PHP_EOL, $item->notes) as $line)
                            {{ $line }}<br/>
                        @endforeach
                    </p>
                </div>
            @else
                <div class="notes-container">
                    <p>No notes</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection