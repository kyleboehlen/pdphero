@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="back" :addiction="$addiction" />

    <div class="app-container relapse-timeline">
        @foreach($relapses as $relapse)
            <span class="time-spacer"></span>
            <span class="time-label">{{ $relapse['datetime_label'] }}</span>
            <div class="timeline-relapse">
                <p>
                    <b><span class="green">{{ $relapse['day_streak'] }}</span> Day Streak: </b> {{ $relapse['notes'] }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection