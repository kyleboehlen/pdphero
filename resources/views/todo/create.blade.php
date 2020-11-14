@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app-header title="To-Do" />

    {{-- Side Nav --}}
    <x-to-do-nav />

    <div class="app-container">

    </div>

    {{-- Navigation Footer --}}
    <x-app-footer />
@endsection