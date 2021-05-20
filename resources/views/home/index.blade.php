@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Home" />

    {{-- Side Nav --}}
    <x-home.nav />

    <div class="app-container no-scroll">
        @foreach($home_icons as $home_icon)
            @if(!in_array($home_icon->id, $hide_array))
                <div class="home-icon" title="{{ $home_icon->desc }}" onclick="location.href='{{ route($home_icon->route) }}'">
                    <img src="{{ asset($home_icon->img) }}" />
                    <p>{{ $home_icon->name }}</p>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection