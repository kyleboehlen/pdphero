@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav show="back" :item="$item"/>

    <div class="app-container reminders-list">
        <h2>Edit Reminders</h2>
        
        @foreach($item->reminders as $reminder)
            <x-todo.reminder :reminder="$reminder" />
        @endforeach

        {{-- Add form --}}
        <x-todo.reminder :todo="$item" />

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
    <x-app.footer highlight="todo" />
@endsection