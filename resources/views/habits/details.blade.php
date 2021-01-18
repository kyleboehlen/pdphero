@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="back|edit|delete" :habit="$habit"/>

    <div class="app-container">
        {{-- Details --}}
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection