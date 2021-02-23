@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav show="list|create|toggle-incomplete|delete" :item="$item" />

    <div class="app-container">
        <x-todo.completed :item="$item" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection