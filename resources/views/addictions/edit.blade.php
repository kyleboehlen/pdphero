@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="back" :addiction="$addiction" />

    <div class="app-container">
        <x-addictions.form :addiction="$addiction" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection