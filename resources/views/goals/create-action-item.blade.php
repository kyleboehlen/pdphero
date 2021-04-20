@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.action-item-nav show="back|create|categories|types" :goal="$goal" />

    <div class="app-container">
        <x-goals.action-item-form :goal="$goal" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection