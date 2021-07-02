@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav show="create|create-from-habit|color-key" />

    <div class="app-container categories-list">
        <h2>Edit Categories</h2>
        
        @foreach($categories as $category)
            <x-todo.category :category="$category" />
        @endforeach

        {{-- Add form --}}
        <x-todo.category />

        {{-- Errors --}}
        @error('name')
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