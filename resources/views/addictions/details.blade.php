@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="list|edit|relapse|milestones|delete" :addiction="$addiction" />

    <div class="app-container">

    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection