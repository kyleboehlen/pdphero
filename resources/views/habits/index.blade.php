@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="create|color-key" />

    <div class="app-container">
        @if($habits->count() == 0)
            <x-habits.empty-habit />
        @else
            @foreach($habits as $habit)
                <x-habits.habit :habit="$habit" />
            @endforeach
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection