@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Home" />

    {{-- Side Nav --}}
    <x-home.nav show="back" />

    <div class="app-container no-scroll">
        @foreach($home_icons as $home_icon)
            @if(!in_array($home_icon->id, $hide_array))
                <div class="home-icon hide-show" title="{{ $home_icon->desc }}">
                    {{-- DO NOT put anything above this form in this div --}}
                    <form action="{{ route('home.hide', ['home' => $home_icon->id]) }}" method="POST">
                        @csrf
                    </form>
            @else
                <div class="home-icon hide-show hidden" title="{{ $home_icon->desc }}">
                    {{-- DO NOT put anything above this form in this div --}}
                    <form action="{{ route('home.show', ['home' => $home_icon->id]) }}" method="POST">
                        @csrf
                    </form>
            @endif
                <img src="{{ asset($home_icon->img) }}" />
                <p>{{ $home_icon->name }}</p>
            </div>
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection