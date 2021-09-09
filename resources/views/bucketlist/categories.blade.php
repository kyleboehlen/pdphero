@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Bucketlist" />

    {{-- Side Nav --}}
    <x-bucketlist.nav show="list|completed" />

    <div class="app-container categories-list">
        <h2>Edit Categories</h2>
        
        @foreach($categories as $category)
            <x-bucketlist.category :category="$category" />
        @endforeach

        {{-- Add form --}}
        <x-bucketlist.category />

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
    <x-app.footer />
@endsection