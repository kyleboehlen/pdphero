<div class="action-item empty">
    <form action="{{ route($route, ['goal' => $goal->uuid]) }}" method="GET">
        {{-- @csrf --}}
        <input type="hidden" name="selected-dropdown" value="ad-hoc-list" />
        <input class="submit-completed" type="checkbox" />
    </form>
    &nbsp;
    <a href="{{ route($route, ['goal' => $goal->uuid, 'selected-dropdown' => 'ad-hoc-list']) }}">
        Add an Action Item
    </a>
    &nbsp;
    <div class="deadline"><p>??/??/??</p></div>
</div>