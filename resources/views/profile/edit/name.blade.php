@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-picture|edit-nutshell|edit-values|manage-membership|log-out" />

    <div class="app-container">
        <form class="edit name" action="{{ route('profile.update.name') }}" method="POST">
            @csrf
        
            <h2>Edit Name</h2><br/><br/>
        
            <input type="text" name="name" maxlength="255" value="{{ $user->name }}" required /><br/><br/><br/>
                
            <a href="{{ route('todo.list') }}">
                <button type="button">Cancel</button>
            </a>
        
            <button type="submit">Submit</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection