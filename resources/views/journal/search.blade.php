@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back|create|search|color-key" />

    <div class="app-container search-results">
        <h2 class="search-title">Search Results for "{{ $keywords }}":</h2>
        
        {{-- Search results --}}
        @foreach($journal_entries as $journal_entry)
            <x-journal.timeline-entry :entry="$journal_entry" :search="$keywords" />
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection