@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Feature Vote" />

    {{-- Side Nav --}}
    <x-feature.nav show="back" />

    <div class="app-container details">

        {{-- Title --}}
        <h2>{{ $feature->name }}</h2>

        {{-- Current vote status --}}
        <h3 class="vote {{ $class }}">I {{ $text }} This Feature</h3><br />

        {{-- Vote form --}}
        <form class="vote" action="{{ route('feature.vote', ['feature' => $feature->uuid]) }}" method="POST">
            @csrf

            {{-- Vote checkboxes --}}
            <div class="vote-checkbox-container">
                <input id="want" class="vote-checkbox" name="want" type="checkbox" @if($class == 'want') checked @endif />
                <input id="dont-care" class="vote-checkbox" name="dont-care" type="checkbox" @if($class == 'dont-care') checked @endif />
                <input id="dont-want" class="vote-checkbox" name="dont-want" type="checkbox" @if($class == 'dont-want') checked @endif />
            </div>
        </form>

        {{-- Desc --}}
        <p class="desc">
            @foreach(explode(PHP_EOL, $feature->desc) as $line)
                {{ $line }}<br/>
            @endforeach
        </p>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection