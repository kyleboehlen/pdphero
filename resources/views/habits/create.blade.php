@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="back|color-key" />

    <div class="app-container">
        <x-habits.form />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection