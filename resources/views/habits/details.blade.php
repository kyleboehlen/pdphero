@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Habits" />

    {{-- Side Nav --}}
    <x-habits.nav show="back|edit|delete" :habit="$habit" />

    <div class="app-container">
        <div class="habit-details">
            {{-- Title --}}
            <h2>{{ $habit->name }}</h2>

            {{-- Requied on details --}}
            <p class="required">{{ $required_on_label }}</p><br/><br/>

            {{-- Progress bar --}}
            <x-app.progress-bar :percent="$habit->strength" /><br/>

            {{-- Notes --}}
            @if(isset($habit->notes))
                <br/><br/>
                <div class="notes-container">
                    @foreach(explode(PHP_EOL, $habit->notes) as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Offset history --}}
            <div class="stats">
                <p><b>Current Streak: </b><i>{{ $habit->getCurrentStreak() }} days</i></p>
                <p><b>Longest Streak: </b><i>{{ $habit->getLongestStreak() }} days</i></p>
            </div>
            <div class="history-container">
                <a href="{{ route('habits.view', ['habit' => $habit->uuid, 'history-offset' => $history_offset + 1]) }}" @if($history_offset >= 52) class="disabled" @endif>
                    <img src="{{ asset("icons/left-arrow-white.png") }}" />
                </a>
                <x-habits.history :habit="$habit" :offset="$history_offset" format="D | n/j" />
                <a 
                    @if($history_offset > 1)
                        href="{{ route('habits.view', ['habit' => $habit->uuid, 'history-offset' => $history_offset - 1]) }}"
                    @elseif($history_offset == 1)
                        href="{{ route('habits.view', ['habit' => $habit->uuid]) }}"
                    @else
                        class="disabled" href="#"
                    @endif
                    >
                    <img src="{{ asset("icons/right-arrow-white.png") }}" />
                </a>
            </div><br/>

            {{-- TODO: Reminders collapsible box --}}
        </div>
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="habits" />
@endsection