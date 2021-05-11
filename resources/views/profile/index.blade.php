@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="edit-name|edit-picture|add-affirmation|edit-nutshell|edit-values|edit-rules|manage-membership|log-out" />

    <div class="app-container">
        <h2>{{ $user->name }}</h2>

        {{-- To-Do: display profile picture, name, # of items/goals/habits accomplished --}}
        <div class="profile-picture-container">
            @isset($user->profile_picture)
                <img src="{{ asset("profile-pictures/$user->profile_picture") }}" />
            @else
                <img class="profile-picture-link" src="{{ asset('icons/profile-white.png') }}" />
            @endisset
        </div>

        <div class="stats-container">
            <ul>
                {{-- Times read through affirmations --}}
                <li>Affirmations Read: <span class="highlight">{{ $user->affirmationsReadLog->count() }}</span></li>

                {{-- Items completed --}}
                <li>To-Do Items Completed: <span class="highlight">{{ $user->completedTodos->count() }}</span></li>

                {{-- To-Do: Goals accomplished --}}
                <li>Goals Accomplished: <span class="highlight">{{ $user->accomplishedGoals->count() }}</span></li>

                {{-- To-Do: Habits created --}}
                <li>Habits Created: <span class="highlight">{{ $user->completedHabits->count() }}</span></li>
            </ul>
        </div>

        <div class="affirmations-container">
            <a href="{{ route('affirmations') }}">
                <div class="read-affirmations">
                    <img src="{{ asset('icons/affirmations-black.png') }}" />
                    <p>Read Your Affirmations</p>
                </div>
            </a>
        </div>

        {{-- Values --}}
        <div class="values-container">
            <h3>Values</h3>

            @isset($user->values)
                <ul>
                    @foreach($user->values as $value)
                        <li>{{ $value }}</li>
                    @endforeach
                </ul>
            @else
                <ul>
                    <a href="{{ route('profile.edit.values') }}" class="create-hover">
                        <li>Click to add!</li>
                        <li>Such as:</li>
                        <li>Honesty</li>
                        <li>Friendship</li>
                        <li>Compassion</li>
                        <li>Etc...</li>
                    </a>
                </ul>
            @endisset
        </div>

        {{-- Nutshell --}}
        <div class="nutshell-container">
            <h3>Nutshell</h3>
            @isset($user->nutshell)
                @foreach(explode(PHP_EOL, $user->nutshell) as $line)
                    <p>{{ $line }}</p>
                @endforeach
            @else
                <a href="{{ route('profile.edit.nutshell') }}" class="create-hover">
                    <p>Click here to add your nutshell; this is where you list the things that are important to you, that you love doing, and that make you who you are!</p>
                </a>
            @endisset
        </div>

        @if($user->getSettingValue($setting::PROFILE_SHOW_RULES))
            {{-- Personal rules --}}
            <div class="rules-container">
                <h3>Personal Rules</h3>

                @isset($user->rules)
                    <ol>
                        @foreach($user->rules as $rule)
                            <li>{{ $rule }}</li>
                        @endforeach
                    </ol>
                @else
                    <ol>
                        <a href="{{ route('profile.edit.rules') }}" class="create-hover">
                            <li>Rules and boundaries help us to do what we really want</li>
                            <li>They're boundries you've already created to protect yourself</li>
                            <li>They even work great with pushy friends or family!</li>
                            <li>Click to add! Such as:</li>
                            <li>I will get to bed before midnight</li>
                            <li>I will not rent to friends or family</li>
                            <li>I will compliment one person a day</li>
                            <li>Etc...</li>
                        </a>
                    </ol>
                @endisset
            </div>
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection