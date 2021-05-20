@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="create|categories|search|color-key" />

    <div class="app-container categories-list">
        <h2>Edit Categories</h2>
        
        @foreach($categories as $category)
            <x-journal.category :category="$category" />
        @endforeach

        {{-- Add form --}}
        <x-journal.category />

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
    <x-app.footer highlight="journal" />
@endsection