@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-name|edit-picture|edit-nutshell|edit-rules|manage-membership|log-out" />

    <div class="app-container values-list">
        <h2>Edit Values</h2>
        
        @isset($user->values)
            @foreach($user->values as $value)
                <x-profile.value :value="$value" />
            @endforeach
        @endisset

        {{-- Add form --}}
        <x-profile.value />

        {{-- Errors --}}
        @error('value')
            @push('scripts')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @endpush
        @enderror
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection