@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.action-item-nav :show="$show" :item="$action_item" />

    <div class="app-container">
        
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection