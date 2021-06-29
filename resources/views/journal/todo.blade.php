@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back|back-day" :todo="$todo"  />

    <div class="app-container entry-details">
        {{-- Completed ToDo --}}
        <p class="journal-category">Completed To-Do</p>

        {{-- Title --}}
        <h2>{{ $todo->title }}</h2>

        {{-- Priority --}}
        <h3 class="priority {{ strtolower(config('todo.priorities')[$todo->priority_id]['name']) }}">{{ config('todo.priorities')[$todo->priority_id]['name'] }} Priority</h3>

        {{-- Body --}}
        <p class="body">
            @if(!is_null($todo->notes))
                @foreach(explode(PHP_EOL, $todo->notes) as $line)
                    {{ $line }}<br/>
                @endforeach
            @else
                No notes
            @endif
        </p>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection