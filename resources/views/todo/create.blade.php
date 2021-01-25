@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav page="list|create-from-habit|create-from-goal|color-key" />

    <div class="app-container">
        @switch($from)
            @case('habit')
                
                @break
            @case('goal')
                
                @break
            @default
                <x-todo.form action="todo.store" title="Create-New-Item" />
        @endswitch
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection