@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-name|edit-picture|edit-values|manage-membership|log-out" />

    <div class="app-container">
        <form class="edit" action="{{ route('profile.update.nutshell') }}" method="POST">
            @csrf
        
            <h2>Edit Nutshell</h2><br/><br/>
        
            <textarea name="nutshell" placeholder="This is your nutshell; It's a place to list the things you that are important to you, the things that make you YOU!">{{ $user->nutshell }}</textarea>
            @error('nutshell')
                <p class="error">{{ $message }}</p>
            @enderror
            <br/><br/>

            <a href="{{ route('profile') }}">
                <button class="cancel" type="button">Cancel</button>
            </a>
        
            <button class="submit" type="submit">Submit</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection