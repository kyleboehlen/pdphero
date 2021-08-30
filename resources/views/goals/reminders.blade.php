@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.action-item-nav show="back-item" :item="$action_item" />

    <div class="app-container reminders-list">
        <h2>Edit Reminders</h2>
        
        @foreach($action_item->reminders as $reminder)
            <x-goals.reminder :reminder="$reminder" />
        @endforeach

        {{-- Add form --}}
        <x-goals.reminder :item="$action_item" />

        {{-- Errors --}}
        @error('date')
            @push('scripts')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @endpush
        @enderror

        @error('time')
            @push('scripts')
                <script>
                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                </script>
            @endpush
        @enderror
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection