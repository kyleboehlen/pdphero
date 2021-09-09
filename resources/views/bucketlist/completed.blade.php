@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Bucketlist" />

    {{-- Side Nav --}}
    <x-bucketlist.nav show="list" />

    <div class="app-container timeline">
        {{-- Timeline --}}
        @foreach($bucketlist_items as $bucketlist_item)
            <span class="time-spacer"></span>
            <span class="time-label">{{ $bucketlist_item->formattedCompletedAt() }}</span>
            <div class="bucketlist-timeline-item">
                <ul class="timeline-item" style="list-style-image: url('{{ url(asset('icons/check-bullet-white.png')) }}');">
                    <li><a class="preview" href="{{ route('bucketlist.view.details', ['bucketlist_item' => $bucketlist_item->uuid]) }}">
                        {{ $bucketlist_item->name }}
                    </a></li>
                </ul>
            </div>
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection