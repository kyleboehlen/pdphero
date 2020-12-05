@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-name|edit-picture|edit-nutshell|manage-membership|log-out" />

    <div class="app-container values-list">
        <h2>Edit Values</h2>
        
        @foreach($user->values as $value)
            <x-profile.value :value="$value" />
        @endforeach

        {{-- Add form --}}
        <x-profile.value />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection