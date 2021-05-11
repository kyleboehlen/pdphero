@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back|edit|color-guide|delete" :entry="$journal_entry"  />

    <div class="app-container">
        {{-- <x-journal.form /> --}}
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection