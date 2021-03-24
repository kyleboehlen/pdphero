@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav :show="$nav_show" :goal="$goal" />

    <div class="app-container">

    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection