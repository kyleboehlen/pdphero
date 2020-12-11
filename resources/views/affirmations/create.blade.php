@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Affirmations" />

    {{-- Side Nav --}}
    <x-affirmations.nav hide="create" />

    <div class="app-container">
        <x-affirmations.form />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection