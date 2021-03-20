@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="back|create" />

    <div class="app-container categories-list">
        <h2>Edit Categories</h2>
        
        @foreach($categories as $category)
            <x-goals.category :category="$category" />
        @endforeach

        {{-- Add form --}}
        <x-goals.category />

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
    <x-app.footer highlight="goals" />
@endsection