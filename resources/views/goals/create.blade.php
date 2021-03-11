@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav show="create" />

    <div class="app-container">
        <x-goals.form :type="$type_id" :future="$future_goal_uuid" :parent="$parent_goal_uuid" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection