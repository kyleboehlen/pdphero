@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back-entry|categories|color-guide" :entry="$journal_entry" />

    <div class="app-container">
        <x-journal.form :entry="$journal_entry" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection