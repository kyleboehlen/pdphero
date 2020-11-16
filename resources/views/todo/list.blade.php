@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav page="list" />

    <div class="app-container">
        @if($to_do_items->count() == 0)
            {{-- Add message about 'no todo items would you like to create one' --}}
        @else
            @foreach($to_do_items as $item)
                <x-todo.item :item="$item" />
            @endforeach
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection