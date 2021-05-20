@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Affirmations" />

    {{-- Side Nav --}}
    <x-affirmations.nav :affirmation="$affirmation" hide="back" />

    <div class="app-container">
        <div class="show">
            <h2>#{{ $page }}</h2>

            <h3>{{ $affirmation->value }}</h3><br/>
        
            @isset($next_uuid)
                <a href="{{ route('affirmations.show', ['affirmation' => $next_uuid]) }}">
                    <button type="button">Next</button>
                </a>
            @else
                <form id="check-read-form" action="{{ route('affirmations.read.check') }}" method="POST">
                    @csrf
                </form>
                <a href="{{ route('affirmations.read.check') }}" onclick="event.preventDefault(); document.getElementById('check-read-form').submit();">
                    <button type="button">Next</button>
                </a>
            @endisset
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection