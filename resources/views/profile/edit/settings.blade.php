@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Settings" />

    {{-- Side Nav --}}
    <x-settings.nav />

    <div class="app-container settings">

        <h2 id="affirmations-settings-header" class="settings">Affirmations</h2>

        {{-- Setting that handles whether or not the affirmations read page is shown --}}
        <x-settings.checkbox-setting :id="$setting::AFFIRMATIONS_SHOW_READ" text="Display the 'Good Job!' page after finishing reading affirmations"/>

        <h2 id="habits-setttings-header" class="settings">Habits</h2>

        {{-- Setting that handles whether or not the affirmations habit is shown --}}
        <x-settings.checkbox-setting :id="$setting::HABITS_SHOW_AFFIRMATIONS_HABIT" text="Show the affirmations habit"/>

        <h2 id="todo-settings-header" class="settings">To-Do</h2>

        {{-- Setting that handles whether or not completed todo items get moved to the bottom of the todo list --}}
        <<x-settings.checkbox-setting :id="$setting::TODO_MOVE_COMPLETED" text="Move completed items to the bottom of the list"/>

        {{-- Setting that handles how long completed todo items stay visible on the todo list --}}
        <x-settings.todo-show-completed-for /><br/>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection