<form class="goal" method="POST" enctype="multipart/form-data"
    @isset($edit_goal)
        action="{{ route('goals.update.goal', ['goal' => $edit_goal->uuid]) }}"
    @else
        action="{{ route('goals.store.goal') }}"
    @endisset>
    @csrf

    {{-- Header --}}
    <h2>@isset($edit_goal) Edit Goal @else Create New {{ $type_name }} @endisset</h2>

    {{-- Hidden elements --}}
    <input type="hidden" name="type" value="{{ $type_id }}" />

    @isset($parent_goal)
        <input type="hidden" name="parent-goal" value="{{ $parent_goal->uuid }}" />
    @endisset
    
    @isset($future_goal)
        <input type="hidden" name="future-goal" value="{{ $future_goal->uuid }}" />
    @endisset

    {{-- Show name as long as it's not a habit based goal --}}
    @if($type_id != $type::HABIT_BASED)
        <input type="text" name="name" placeholder="Name" maxlength="255" required
        @if(!is_null($edit_goal))
            value="{{ $edit_goal->name }}"
        @elseif(!is_null($future_goal))
            value="{{ $future_goal->name }}"
        @else
            value="{{ old('name') }}"
        @endif>

        @error('name')
            <p class="error">{{ $message }}</p>
        @enderror
    @else
        {{-- If it is, show the drop down of habits --}}
        <select id="goal-habit-select" name="habit" required>
            @if(is_null($edit_goal) && is_null(old('habit')))
                <option disabled selected value="0"> -- Select a Habit -- </option>
            @endif

            @foreach($habits as $habit)
                <option value="{{ $habit->uuid }}"
                    @if(!is_null($edit_goal) && $edit_goal->habit_id == $habit->id)
                        selected
                    @elseif(old('habit') == $habit->uuid)
                        selected
                    @endif
                >{{ $habit->name }}</option>
            @endforeach
        </select>

        @error('habit')
            <p class="error">{{ $message }}</p>
        @enderror
    @endif
    <br/><br/>

    {{-- Goal category --}}
    <select name="category">
        <option value="no-category">No Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->uuid }}"
                @if(!is_null($edit_goal) && $edit_goal->category_id == $category->id)
                    selected
                @elseif(!is_null($future_goal) && $future_goal->category_id == $category->id)
                    selected
                @elseif(old('category') == $category->uuid)
                    selected
                @endif
            >{{ $category->name }}</option>
        @endforeach
    </select>
    @error('category')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    {{-- Ad-hoc/manaul goal options --}}
    @if($type_id == $type::ACTION_AD_HOC || $type_id == $type::MANUAL_GOAL)
        {{-- How many manual/action items  --}}
        <label for="custom-times" @if($type_id == $type::MANUAL_GOAL) class="manual-times" @else style="margin-left: 0;" @endif >Complete </label><input class="{{ $type_id == $type::MANUAL_GOAL ? 'manual-times' : 'ad-hoc-times' }}" name="custom-times" type="number"
        @isset($edit_goal)
            value="{{ $edit_goal->custom_times }}"
        @else
            value="1"
        @endisset><label class="time-period" for="time-period"> {{ $type_id == $type::ACTION_AD_HOC ? 'action' : 'manual' }} items</label>
        @if($type_id == $type::ACTION_AD_HOC)
            <br/><br/>
            <select name="time-period">
                @foreach($time_periods as $value => $time_period)
                    <option value="{{ $value }}"
                        @if(!is_null($edit_goal) && $edit_goal->time_period_id == $value)
                            selected
                        @elseif(old('time-period') == $value)
                            selected
                        @endif
                    >{{ $time_period['name'] }}</option>
                @endforeach
            </select>
        @endif
        <br/><br/>
    @endif

    {{-- Goal dates --}}
    @if($type_id != $type::FUTURE_GOAL)
        @if($type_id == $type::HABIT_BASED)
            <input id="goal-habit-strength" type="number" class="habit-strength" name="habit-strength" min="1" max="100" required
            @isset($edit_goal)
                value="{{ $edit_goal->habit_strength }}"
            @else
                value="100"
            @endisset /><span class="percent-label">% </span><label class="habit-strength" for="habit-strength">Habit Strength</label><br/>
        @else
            <label for="start-date"> Start goal: </label><input type="date" name="start-date" required
                @isset($edit_goal)
                    value="{{ $edit_goal->start_date }}"
                @else
                    value="{{ old('start-date') }}"
                @endisset />
        @endif

        {{-- End date --}}
        <br/><br/>
        <label for="end-date">Complete by: </label>
        <input type="date" name="end-date" required
            @isset($edit_goal)
                value="{{ $edit_goal->end_date }}"
            @endisset />
        @if($type_id == $type::HABIT_BASED)
            {{-- Habit strength script/label --}}
            <p id="goal-habit-strength-label"></p><br/><br/>
            <script>
                $(document).ready(function(){
                    // Habit goals get strength for create form
                    $('#goal-habit-select').add('#goal-habit-strength').change(function(){
                        var habit = $('#goal-habit-select').find(':selected').val();
                        var strength = $('#goal-habit-strength').val();
    
                        if(parseInt(habit) != 0 && parseInt(strength) != 0)
                        {
                            $.ajax({
                                type: 'POST',
                                url: window.location.origin + '/habits/soonest/' + habit + '/' + strength,
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                }
                            }).done(function(date){
                                $('#goal-habit-strength-label').html('The soonest you can hit ' + strength + '% strength is ' + date);
                                $('#goal-habit-strength-label').show();
                            });
                        }
                    });
                });
            </script>
        @else
            <br/><br/>
        @endif
    @endif

    {{-- Goal image --}}
    <label for="image">Goal image:</label>
    <input type="file" name="goal-image" accept="image/png, image/jpeg, image/jpg" />
    @isset($future_goal)
        @if($future_goal->use_custom_img)
            <p id="future-goal-image-label">If no image is selected one for future goal '{{ $future_goal->name }}' will be used</p>
        @endif
    @endisset
    @error('image')
        <p class="error">{{ $message }}</p>
    @else
    @enderror
    <br/><br/>

    {{-- Goal reason --}}
    <textarea class="reason" name="reason" required
        placeholder="Put the reason you want to accomplish this goal here. What do you envision things looking like when you accomplish it? It's important to reference your goal reason on days where you just don't feel like working on it!"
    @isset($edit_goal)>{{ $edit_goal->reason }}</textarea>@else @isset($future_goal)>{{ $future_goal->reason }}</textarea>@else>{{ old('reason') }}</textarea>@endisset @endisset
    @error('reason')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    {{-- Action plan goals push to todo options --}}
    @if(in_array($type_id, [$type::ACTION_AD_HOC, $type::ACTION_DETAILED, $type::PARENT_GOAL]))
        <span class="show-todo" title="If this is selected your goal action items will automatically show up in your to-do list">
            <input id="goal-show-todo" class="show-todo" type="checkbox" name="show-todo" @isset($edit_goal) @if($edit_goal->default_show_todo) checked @endif @endisset /> List action items on To-Do<br/>
            <input id="default-days-before-due-input" type="number" name="show-todo-days-before"
                @isset($edit_goal)
                    @if(!is_null($edit_goal->default_todo_days_before))
                        value="{{ $edit_goal->default_todo_days_before }}"
                    @else
                        {{-- Maybe check for a parent goal value here --}}
                        value="7" 
                    @endif

                    @if(!$edit_goal->default_show_todo)
                        disabled>
                        <span id="default-days-before-due-label" class="disabled">
                    @else
                        >
                        <span id="default-days-before-due-label">
                    @endif
                @else
                    disabled
                    value="7">
                    <span id="default-days-before-due-label" class="disabled">
                @endisset days before due </span>
        </span><br/><br/>
    @endif

    {{-- Don't show notes for habit based goals, use the habit notes --}}
    @if($type_id != $type::HABIT_BASED)
        <textarea name="notes"
            placeholder="Any other notes you have for this goal go here!"
        @isset($edit_goal)>{{ $edit_goal->notes }}</textarea>@else @isset($future_goal)>{{ $future_goal->notes }}</textarea>@else>{{ old('notes') }}</textarea>@endisset @endisset

        @error('notes')
            <p class="error">{{ $message }}</p>
        @enderror

        <br/><br/><br/>
    @else
        <br/><br/>
    @endif

    {{-- Buttons --}}
    <a href="{{ route('goals') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>

</form>
