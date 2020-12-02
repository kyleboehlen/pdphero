@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="edit-name|edit-picture|edit-nutshell|edit-values|manage-membership|log-out" />

    <div class="app-container">
        <h2>{{ $user->name }}</h2>

        {{-- To-Do: display profile picture, name, # of items/goals/habits accomplished --}}
        <div class="profile-picture-container">
            <img @isset($user->profile_picture) src="{{ asset("profile-pictures/$user->profile_picture") }}" @else src="{{ asset('icon/profile-white.png') }}" @endisset />
        </div>

        <div class="stats-container">
            <ul>
                {{-- Items completed --}}
                <li>To-Do Items Completed: <span class="highlight">{{ $user->todos->count() }}</span></li>

                {{-- To-Do: Goals accomplished --}}
                <li>Goals Accomplished: <span class="highlight">15</span></li>

                {{-- To-Do: Habits created --}}
                <li>Habits Created: <span class="highlight">5</span></li>
            </ul>
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
                    <a href="{{ route('profile.edit.values') }}">
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
                <a href="{{ route('profile.edit.nutshell') }}">
                    <p>Click here to add your nutshell; this is where you list the things that are important to you, that you love doing, and that make you who you are!</p>
                </a>
            @endisset
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection