@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Profile"  icon="settings" route="profile.edit.settings" />

    {{-- Side Nav --}}
    <x-profile.nav show="back|edit-name|edit-picture|edit-nutshell|edit-values|manage-membership|log-out" />

    <div class="app-container rules-list">
        <h2>Edit Personal Rules</h2>
        
        @isset($user->rules)
            @foreach($user->rules as $rule)
                <x-profile.rule :rule="$rule" />
            @endforeach
        @endisset

        {{-- Add form --}}
        <x-profile.rule />

        {{-- Errors --}}
        @error('rule')
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