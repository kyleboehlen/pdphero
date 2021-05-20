@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="back-goal|create|categories|types" :goal="$goal" />

    <div class="app-container">
        <x-goals.form :goal="$goal" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection