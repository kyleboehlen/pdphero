@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-picture|edit-nutshell|edit-values|edit-rules|manage-membership|log-out" />

    <div class="app-container">
        <form class="edit single-text" action="{{ route('profile.sms.update') }}" method="POST">
            @csrf
        
            <h2>{{ !is_null($user->sms_verified_at) ? 'Update' : 'Add' }} Phone Number</h2><br/><br/>
        
            <input type="text" name="phone-number" minlength="10" value="{{ old('phone-number') ?? (!is_null($user->sms_verified_at) ? $user->sms_number : '') }}" required />
            @error('phone-number')
                <p class="error">{{ $message }}</p>
            @enderror
                
            <a href="{{ route('profile') }}">
                <button class="cancel" type="button">Cancel</button>
            </a>
        
            <button class="submit" type="submit">Submit</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection