@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Bucketlist" />

    {{-- Side Nav --}}
    <x-bucketlist.nav show="list|edit-categories" />

    <div class="app-container">
        <x-bucketlist.form />
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection