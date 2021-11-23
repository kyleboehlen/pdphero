@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="create" />

    <div class="app-container list">
        @foreach($addictions as $addiction)
            <x-addictions.card :addiction="$addiction" />
        @endforeach

        @if($addictions->count() == 0)
            <x-addictions.empty-card />
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection