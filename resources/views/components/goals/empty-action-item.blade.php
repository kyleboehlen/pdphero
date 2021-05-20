<div class="action-item empty">
    <form action="{{ route($route, ['goal' => $goal->uuid, 'selected-dropdown' => 'ad-hoc-list']) }}" method="GET">
        @csrf
        <input class="submit-completed" name="achieved" type="checkbox" />
    </form>
    &nbsp;
    <a href="{{ route($route, ['goal' => $goal->uuid, 'selected-dropdown' => 'ad-hoc-list']) }}">
        Add an Action Item
    </a>
    &nbsp;
    <div class="deadline"><p>??/??/??</p></div>
</div>