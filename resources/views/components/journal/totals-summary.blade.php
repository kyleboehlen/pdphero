<div class="summary totals">
    {{-- Totals title --}}
    <h2>{{ ucwords($month) }} Totals</h2>

    <ul style="list-style-image: url('{{ url(asset('icons/check-bullet-white.png')) }}');">
        @if($todo_count > 0)
            <li><b>{{ $todo_count }}</b> <i>To-Do Items Completed</i></li>
        @endif
        @if($habit_count > 0)
            <li><b>{{ $habit_count }}</b> <i>Habits Performed</i></li>
        @endif
        @if($goal_count > 0)
            <li><b>{{ $goal_count }}</b> <i>Goals Achieved</i></li>
        @endif
        @if($action_item_count > 0)
            <li><b>{{ $action_item_count }}</b> <i>Action Items Achieved</i></li>
        @endif
        @if($journal_entry_count > 0)
            <li><b>{{ $journal_entry_count }}</b> <i>Journal Entries</i></li>
        @endif
        @if($affirmations_count > 0)
            <li><b>{{ $affirmations_count }}</b> <i>Affirmations Read</i></li>
        @endif
    </ul>
</div>