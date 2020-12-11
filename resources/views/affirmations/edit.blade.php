@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Affirmations" />

    {{-- Side Nav --}}
    <x-affirmations.nav :affirmation="$affirmation" hide="edit" />

    <div class="app-container">
        <x-affirmations.form :affirmation="$affirmation" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection