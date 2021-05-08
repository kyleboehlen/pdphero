@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back" />

    <div class="app-container">
        <div class="color-guide">
            <div class="color-key"><input class="priority positive" type="checkbox" checked disabled /><br/>Positive Mood</div>
            <div class="color-key"><input class="priority neutral" type="checkbox" checked disabled /><br/>Neutral Mood</div>
            <div class="color-key"><input class="priority negative" type="checkbox" checked disabled /><br/>Negative Mood</div>
            <div class="color-key"><input class="priority default" type="checkbox" checked disabled /><br/>Default Mood</div>
            <br/>
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection