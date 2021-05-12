@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back|edit|color-guide|delete" :entry="$journal_entry"  />

    <div class="app-container entry-details">
        {{-- Journal Category --}}
        <p class="journal-category">{{ !is_null($journal_entry->category) ? $journal_entry->category->name : 'Journal Entry' }}</p>

        {{-- Title --}}
        <h2>{{ $journal_entry->title }}</h2>

        {{-- Mood --}}
        <h3 class="mood {{ strtolower(config('journal.moods')[$journal_entry->mood_id]['name']) }}">{{ config('journal.moods')[$journal_entry->mood_id]['name'] }} Mood</h3>

        {{-- Body --}}
        <p class="body">
            @foreach(explode(PHP_EOL, $journal_entry->body) as $line)
                {{ $line }}<br/>
            @endforeach
        </p>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection