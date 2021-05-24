@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Settings" />

    {{-- Side Nav --}}
    <x-settings.nav />

    <div class="app-container settings">

        <h2 id="affirmations-settings-header" class="settings">Affirmations</h2>

        {{-- Setting that handles whether or not the affirmations read page is shown --}}
        <x-settings.checkbox-setting :id="$setting::AFFIRMATIONS_SHOW_READ" text="Display the 'Good Job!' page after finishing reading affirmations" />

        <h2 id="goals-settings-header" class="settings">Goals</h2>

        {{-- Empty action plan item/ad hoc item settings --}}
        <x-settings.options-setting :id="$setting::SHOW_EMPTY_ACTION_ITEM" text="Show add action plan item at the | of action plans" />
        <x-settings.options-setting :id="$setting::SHOW_EMPTY_AD_HOC_ITEM" text="Show add ad-hoc item at the | of ad-hoc lists" />

        <h2 id="habits-setttings-header" class="settings">Habits</h2>

        {{-- Setting that handles whether or not the affirmations habit is shown --}}
        <x-settings.checkbox-setting :id="$setting::HABITS_SHOW_AFFIRMATIONS_HABIT" text="Show the affirmations habit" />

        {{-- Setting that handles whether or not the journaling habit is shown --}}
        <x-settings.checkbox-setting :id="$setting::HABITS_SHOW_JOURNALING_HABIT" text="Show the journaling habit" />

        {{-- Setting that determines whether to show habit history for rolling 7 days or current week --}}
        <x-settings.options-setting :id="$setting::HABITS_DAYS_TO_DISPLAY" text="Show habit history for | " />

        @if($user->getSettingValue($setting::HABITS_DAYS_TO_DISPLAY) == $setting::HABITS_CURRENT_WEEK)
            {{-- Setting that determines what the first day of "current week" should be --}}
            <x-settings.options-setting :id="$setting::HABITS_START_OF_WEEK" text="Use | as the first day of the week" />
        @endif

        <h2 id="profile-settings-header" class="settings">Profile</h2>

        {{-- Setting that determines whether or not personal rules show on the users profile --}}
        <x-settings.checkbox-setting :id="$setting::PROFILE_SHOW_RULES" text="Show personal rules on my profile" />

        <h2 id="todo-settings-header" class="settings">To-Do</h2>

        {{-- Setting that handles whether or not completed todo items get moved to the bottom of the todo list --}}
        <x-settings.checkbox-setting :id="$setting::TODO_MOVE_COMPLETED" text="Move completed items to the bottom of the list" />

        {{-- Setting that handles how long completed todo items stay visible on the todo list --}}
        <x-settings.numeric-setting :id="$setting::TODO_SHOW_COMPLETED_FOR" text="Show completed items on the list for | hours" />

        {{-- Empty todo setting --}}
        <x-settings.options-setting :id="$setting::SHOW_EMPTY_TODO_ITEM" text="Show add todo item at the | of the todo list" />

        <br/>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection