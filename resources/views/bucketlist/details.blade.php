@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Bucketlist" />

    {{-- Side Nav --}}
    @if($item->completed)
        <x-bucketlist.nav show="list|mark-incomplete|delete" :item="$item"/>
    @else
        <x-bucketlist.nav show="list|mark-completed|edit|delete" :item="$item" />
    @endif

    <div class="app-container">
        <div class="bucketlist-details">
            {{-- Category --}}
            @if(!is_null($item->category))
                <p class="bucketlist-category">{{ $item->category->name }}</p>
            @else
                <br/>
            @endif

            {{-- Header name --}}
            <h2>{{ $item->name }}</h2>

            {{-- Notes --}}
            @if(isset($item->notes))
                <div class="details-container">
                    <p>
                        @foreach(explode(PHP_EOL, $item->notes) as $line)
                            {{ $line }}<br/>
                        @endforeach
                    </p>
                </div>
            @else
                <div class="details-container">
                    <p>No details</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection