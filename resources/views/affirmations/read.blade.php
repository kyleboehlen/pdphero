@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Affirmations" />

    {{-- Side Nav --}}
    <x-affirmations.nav />

    <div class="app-container">
        <div class="read">
            <h2>Good Job!</h2>

            <h3>It feels really good to read all the way through your affirmations! Wanna do it again?</h3><br/>
        
            <a href="{{ route('affirmations') }}">
                <button type="button">Again!</button>
            </a>
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection