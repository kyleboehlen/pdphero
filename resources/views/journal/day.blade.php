@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="back|create|search|color-key" :date="$date" />

    <div class="app-container day-timeline">
        {{-- Filter select --}}
        <div class="selector">
            <select id="journal-filter-selector">
                @foreach ($filter_dropdown as $filter_value => $filter_name)
                    <option value="{{ $filter_value }}">{{ $filter_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Affirmations Overview --}}
        @if($affirmations_count > 0)
            <div class="summary summary-hide summary-show-affirmations" id="affirmations-journal-summary">
                <ul style="list-style-image: url('{{ url(asset('icons/check-bullet-white.png')) }}');">
                    <li><b>Affirmations Read:</b> <i>{{ $affirmations_count }} Times</i></li>
                </ul>
            </div>
        @endif

        {{-- Habits Overview --}}
        @if($completed_habits->count() > 0 || $skipped_habits->count() > 0 || $missed_habits->count() > 0)
            <div class="summary summary-hide summary-show-habits" id="habits-journal-summary">
                <h2>Habits</h2>

                @if($completed_habits->count() > 0)
                    <p>Completed:</p>
                    <ul style="list-style-image: url('{{ url(asset('icons/check-bullet-white.png')) }}');">
                        @foreach($completed_habits as $completed_habit)
                            <li><b>{{ $completed_habit->habit->name }}:</b> <i>{{ $completed_habit->times }} Times @if(!is_null($completed_habit->notes)) ({{ $completed_habit->notes }}) @endif</i></li>
                        @endforeach
                    </ul>
                @endif

                @if($skipped_habits->count() > 0)
                    <p>Skipped:</p>
                    <ul style="list-style-image: url('{{ url(asset('icons/skip-bullet.png')) }}');">
                        @foreach($skipped_habits as $skipped_habit)
                            <li><b>{{ $skipped_habit->habit->name }}</b> <i>({{ $skipped_habit->notes }})</i></li>
                        @endforeach
                    </ul>
                @endif

                @if($missed_habits->count() > 0)
                    <p>Missed:</p>
                    <ul style="list-style-image: url('{{ url(asset('icons/missed-bullet.png')) }}');">
                        @foreach($missed_habits as $missed_habit)
                            <li><b>{{ $missed_habit->habit->name }}</b> <i>({{ $missed_habit->notes }})</i></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
        
        {{-- Timeline --}}
        @foreach($timeline_array as $timeline_obj)
            @if($timeline_obj instanceof \App\Models\Journal\JournalEntry)
                {{-- Journal entry component --}}
                <x-journal.timeline-entry :entry="$timeline_obj" />
            @else
                {{-- Timeline item component --}}
                <x-journal.timeline-item :item="$timeline_obj" />
            @endif
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection