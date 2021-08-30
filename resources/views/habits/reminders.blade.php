@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="back-habit" :habit="$habit" />

    <div class="app-container reminders-list">
        <h2>Edit Reminders</h2>
        
        @foreach($habit->reminders as $reminder)
            <x-habits.reminder :reminder="$reminder" />
        @endforeach

        {{-- Add form --}}
        <x-habits.reminder :habit="$habit" />

        {{-- Errors --}}
        @error('time')
            @push('scripts')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @endpush
        @enderror
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection