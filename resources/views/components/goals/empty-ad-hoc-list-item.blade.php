<div class="ad-hoc-list-item empty">
    <a class="set-deadline-icon" href="{{ route('goals.create.action-item', ['goal' => $goal->uuid]) }}">
        <img src="{{ asset('icons/calendar-white.png') }}" />
    </a>
    &nbsp;
    <a href="{{ route('goals.create.action-item', ['goal' => $goal->uuid]) }}">
        Add an Ad Hoc Item
    </a>
    &nbsp;
    <a class="set-deadline-link" href="{{ route('goals.create.action-item', ['goal' => $goal->uuid]) }}">
        <div class="deadline">
            <p>??/??/??</p>
        </div>
    </a>
</div>