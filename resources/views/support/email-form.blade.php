@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Support" />

    {{-- Side Nav --}}
    <x-support.nav />

    <div class="app-container">
        <form class="email" action="{{ route('support.email.submit') }}" method="POST">
            @csrf
        
            <h2>Support Email</h2>

            @error('message')
                <p class="error">{{ $message }}</p>
            @enderror

            <textarea name="message" placeholder="Please let us know what support can help you with today :)">{{ old('message') }}</textarea>
            <br/><br/>

            <a href="{{ route('home') }}">
                <button class="cancel" type="button">Cancel</button>
            </a>
        
            <button class="submit" type="submit">Send</button>
        </form>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection