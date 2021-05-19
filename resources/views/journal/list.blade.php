@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Journal" />

    {{-- Side Nav --}}
    <x-journal.nav show="create|search|categories|color-key" />

    <div class="app-container month-list">
        <div class="selector">
            <select class="journal-selector" id="month-selector">
                @foreach ($month_dropdown as $month_value => $month_name)
                    <option value="{{ $month_value }}" @if($month_value == $month) selected @endif>{{ $month_name }}</option>
                @endforeach
            </select>
            <select class="journal-selector" id="year-selector">
                @foreach($year_dropdown as $year_value)
                    <option value="{{ $year_value }}" @if($year_value == $year) selected @endif>{{ $year_value }}</option>
                @endforeach
            </select>
        </div>

        {{-- Totals Summary --}}
        <x-journal.totals-summary :array="$content_array" :month="$month" />

        {{-- Day summaries --}}
        @foreach($content_array as $array)
            <x-journal.summary :array="$array" />
        @endforeach
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="journal" />
@endsection