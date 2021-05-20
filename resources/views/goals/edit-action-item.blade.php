@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.action-item-nav show="back-goal|back-item" :item="$action_item" />

    <div class="app-container">
        <x-goals.action-item-form :item="$action_item" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection