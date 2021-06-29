@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Feature Vote" />

    {{-- Side Nav --}}
    <x-feature.nav />

    <div class="app-container">
        @if($features->count() == 0)
            {{-- Maybe add and empty message if we ever don't have any features in the queue? I'm not fucking worrying about it right now, sue me. --}}
        @else
            @foreach($features as $feature)
                <x-feature.feature :feature="$feature" />
            @endforeach
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection