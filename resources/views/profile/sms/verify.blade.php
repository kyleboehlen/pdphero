@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-picture|edit-nutshell|edit-values|edit-rules|sms-number|manage-membership|log-out" />

    <div class="app-container">
        <form class="edit single-text" action="{{ route('profile.sms.verify') }}" method="POST">
            @csrf
        
            <h2>Verify Phone Number</h2>
            <p class="verification">Please enter the verification code we just sent to you</p>
            <br/>
        
            <span class="verification">
                <p>PDP-</p>
                <input type="text" name="verify-part-one" id="verify-part-one" maxlength="3" value="{{ old('verify-part-one') }}" required />
                <p>-</p>
                <input type="text" name="verify-part-two" id="verify-part-two" maxlength="3" value="{{ old('verify-part-two') }}" required />
            </span>

            @error('verify-part-one')
                <p class="error">{{ $message }}</p>
            @enderror

            @error('verify-part-two')
                <p class="error">{{ $message }}</p>
            @enderror
                
            <a href="{{ route('profile') }}">
                <button class="cancel" type="button">Cancel</button>
            </a>
        
            <button class="submit" type="submit">Verify</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection