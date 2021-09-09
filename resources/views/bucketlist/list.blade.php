@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Bucketlist" />

    {{-- Side Nav --}}
    <x-bucketlist.nav show="create|completed|edit-categories" />

    <div class="app-container">
        @if(count($category_filter_array) > 0)
            <div class="selector">
                <select id="bucketlist-category-selector">
                    <option value="all-categories">Show All</option>

                    @foreach ($categories as $category)
                        @if(in_array($category->id, $category_filter_array))
                            <option value="{{ $category->uuid }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

        @if($bucketlist_items->count() == 0)
            <x-bucketlist.empty-list-item />
        @else
            @if($user->getSettingValue($setting::SHOW_EMPTY_BUCKETLIST_ITEM) == $setting::TOP_OF_LIST)
                <x-bucketlist.empty-list-item />
            @endif

            @foreach($bucketlist_items as $item)
                <x-bucketlist.item :item="$item" />
            @endforeach

            @if($user->getSettingValue($setting::SHOW_EMPTY_BUCKETLIST_ITEM) == $setting::BOTTOM_OF_LIST)
                <x-bucketlist.empty-list-item />
            @endif
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="todo" />
@endsection