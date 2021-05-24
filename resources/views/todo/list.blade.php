@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="To-Do" />

    {{-- Side Nav --}}
    <x-todo.nav show="create|create-from-habit|create-from-goal|color-key" />

    <div class="app-container">
        @if($to_do_items->count() == 0)
            <x-todo.empty-list-item />
        @else
            @if($user->getSettingValue($setting::SHOW_EMPTY_TODO_ITEM) == $setting::TOP_OF_LIST)
                <x-todo.empty-list-item />
            @endif

            @foreach($to_do_items as $item)
                <x-todo.item :item="$item" />
            @endforeach

            @if($user->getSettingValue($setting::SHOW_EMPTY_TODO_ITEM) == $setting::BOTTOM_OF_LIST)
                <x-todo.empty-list-item />
            @endif
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection