<form class="action-item" method="POST"
    @isset($action_item)
        action="{{ route('goals.update.action-item', ['action_item' => $action_item->uuid]) }}"
    @else
        action="{{ route('goals.store.action-item', ['goal' => $goal->uuid]) }}"
    @endisset>
    @csrf

    {{-- Header --}}
    <h2>@isset($action_item) Edit&nbsp;@else Create&nbsp;@endisset{{ $goal->name }} Action Item</h2>

    {{-- Name --}}
    <input type="text" name="name" placeholder="Name" maxlength="255" required
    @isset($action_item)
        value="{{ $action_item->name }}"
    @else
        value="{{ old('name') }}"
    @endif>

    @error('name')
        <p class="error">{{ $message }}</p>
    @enderror

    <br/><br/>

    {{-- Show deadline if it's not ad hoc --}}
    @if($goal->type_id != $goal_type::ACTION_AD_HOC)
        <label for="deadline" class="deadline"> Deadline: </label><input type="date" name="deadline" required
        @isset($action_item)
            value="{{ $action_item->deadline }}"
        @else
            value="{{ old('deadline') }}"
        @endisset />

        @error('deadline')
            <p class="error">{{ $message }}</p>
        @enderror

        <br/><br/>
    @endif

    <span class="show-todo" title="Override the goal's show on To-Do list settings for this action item only">
        <input id="override-show-todo" class="show-todo" type="checkbox" name="override-show-todo"
            @isset($action_item) @if(!is_null($action_item->override_show_todo)) checked @endif @endisset /> Override goal show To-Do settings<br/>
        <span id="show-todo-options">
            <input id="action-item-show-todo" class="show-todo" type="checkbox" name="show-todo"
                @isset($action_item)
                    @if(!is_null($action_item->override_show_todo) && $action_item->override_show_todo)
                        checked
                    @elseif(!is_null($goal->default_show_todo) && $goal->default_show_todo)
                        checked
                    @endif
                @else
                    @if(!is_null($goal->default_show_todo) && $goal->default_show_todo)
                        checked
                    @endif
                @endisset /> Show action items on To-Do List<br/>
            <input id="days-before-due-input" type="number" name="show-todo-days-before"
                @isset($action_item)
                    @if(!is_null($action_item->override_show_todo) && $action_item->override_show_todo)
                        value="{{ $action_item->override_todo_days_before }}"
                    @elseif(!is_null($goal->default_show_todo) && $goal->default_show_todo)
                        value="{{ $goal->default_todo_days_before }}"
                    @else
                        value="7"
                    @endif
                @else
                    @if(!is_null($goal->default_show_todo) && $goal->default_show_todo)
                        value="{{ $goal->default_todo_days_before }}"
                    @else
                        value="7"
                    @endif
                @endisset/><span id="days-before-due-label"> days before due </span>
        </span>
    </span><br/>

    <br/>
    <textarea name="notes"
        placeholder="Any action item specific notes go here :)"
    >@isset($action_item){{ $action_item->notes }}@else{{ old('notes') }}@endisset</textarea>
    @error('notes')
        <p class="error">{{ $message }}</p>
    @enderror

    <a href="{{ route('goals.view.goal', ['goal' => $goal->uuid]) }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>