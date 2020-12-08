@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Settings" />

    {{-- Side Nav --}}
    <x-settings.nav />

    <div class="app-container settings">

        <h2 id="todo-settings-header" class="settings">To-Do Settings</h2>

        {{-- Setting that handles whether or not completed todo items get moved to the bottom of the todo list --}}
        <x-settings.todo-move-completed />

        {{-- Setting that handles how long completed todo items stay visible on the todo list --}}
        <x-settings.todo-show-completed-for />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection