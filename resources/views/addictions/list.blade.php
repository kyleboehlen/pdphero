@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="create" />

    <div class="app-container">

    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection