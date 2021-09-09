@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Bucketlist" />

    {{-- Side Nav --}}
    <x-bucketlist.nav show="back|list|edit-categories" :item="$item" />

    <div class="app-container">
        <x-bucketlist.form :item="$item" />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection