@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav page="list|create-from-habit|create-from-goal|edit-categories|color-key" />

    <div class="app-container">
        @isset($create_type)
            <x-todo.form :create="$create_type"/>
        @else
            <x-todo.form />
        @endisset
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection