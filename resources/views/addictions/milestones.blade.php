@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Addictions" />

    {{-- Side Nav --}}
    <x-addictions.nav show="back|create-milestone" :addiction="$addiction" />

    <div class="app-container milestones-list">
        <h2>Edit Milestones</h2>
        
        @foreach($addiction->milestones as $milestone)
            <x-addictions.milestone :milestone="$milestone" />
        @endforeach

        @if($addiction->milestones->count() == 0)
            <div class="milestone empty">
                <a href="{{ route('addiction.milestone.create', ['addiction' => $addiction]) }}">
                    Create Milestone
                </a>
            </div>
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer />
@endsection