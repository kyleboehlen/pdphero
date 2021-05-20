@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="back|create|color-key|delete" :habit="$habit" />

    <div class="app-container no-scroll">
        <x-habits.form :habit="$habit" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection