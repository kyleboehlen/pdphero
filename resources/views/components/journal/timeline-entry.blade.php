<span class="time-spacer summary-hide summary-show-journal-entry"></span>
<span class="time-label summary-hide summary-show-journal-entry">{{ $journal_entry->display_time }}</span>
<div class="summary summary-hide summary-journal-entry summary-show-journal-entry">
    <div class="text">
        <b>{{ $journal_entry->title }}: </b>
        <a class="preview" href="{{ route('journal.view.entry', ['journal_entry' => $journal_entry]) }}">
            <i>{{ $journal_entry->body }}</i>
        </a>
    </div>
    <div class="mood {{ strtolower(config('journal.moods')[$journal_entry->mood_id]['name']) }}"></div>
</div>