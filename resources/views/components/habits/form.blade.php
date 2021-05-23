<form class="habit" @isset($habit) action="{{ route('habits.update', ['habit' => $habit->uuid]) }}" @else action="{{ route('habits.store') }}" @endisset method="POST">
    @csrf

    {{-- Header --}}
    <h2>@isset($habit) Edit Habit @else Create New Habit @endisset</h2>

    {{-- Habit title --}}
    <input type="text" name="title" placeholder="Title" maxlength="255" 
        @isset($habit) value="{{ $habit->name }}" @else value="{{ old('title') }}" @endisset
        @if($habit->type_id != $type::USER_GENERATED) disabled @endif
    required />
    @if($habit->type_id != $type::USER_GENERATED)
        <input type="hidden" name="title" value="{{ $habit->name }}" />
    @endif
    @error('title')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/>

    {{-- Times daily vs every x days --}}
    <h3>Habit required on:</h3>

    <div class="required-on">
        <div class="day-of-week-container">
            @foreach($carbon_period as $carbon)
                <div class="day-of-week @isset($habit) @if(is_null($habit->days_of_week)) disabled @endif @endisset">
                    <p>{{ $carbon->format('D') }}</p>
                    <input class="day-of-week-checkbox" type="checkbox" name="days-of-week[]" value="{{ $carbon->format('w') }}"
                        @isset($habit)
                            @isset($habit->days_of_week)
                                @if(in_array($carbon->format('w'), $habit->days_of_week))
                                    checked
                                @endif
                            @else
                                disabled
                            @endisset
                        @endisset
                    />
                </div>
            @endforeach
        </div>

        <p>--or--</p>
        
        <p class="every-x-days @isset($habit) @if(is_null($habit->every_x_days)) disabled @endif @else disabled @endisset" required>Every
            <span class="every-x-days-clickable">
            <input id="every-x-days-input" type="number" name="every-x-days" min="1" max="10"
                @isset($habit)
                    @isset($habit->every_x_days)
                        value="{{ $habit->every_x_days }}"
                    @else
                        disabled
                    @endisset
                @else
                    disabled
                @endisset
            /></span> day(s)
        </p>
    </div><br/><br/>

    {{-- Times daily numeric input --}}
    <span class="times-daily">
        Complete <input type="number" name="times-daily" min="1" max="100" required
            @isset($habit)
                value="{{ $habit->times_daily }}"
            @else
                value="1"
            @endisset
        /> time(s) daily
    </span><br/><br/>

    {{-- Show todo checkbox --}}
    @if($habit->type_id == $type::USER_GENERATED)
        <span class="show-todo" title="If this is selected your habit will automatically show up in your to-do list">
            <input class="show-todo" type="checkbox" name="show-todo" @isset($habit) @if($habit->show_todo) checked @endif @else checked @endif /> Show on To-Do List
        </span><br/><br/>
    @endif

    <br/>

    {{-- Notes --}}
    <textarea name="notes" placeholder="Any notes for your habit go here!">@isset($habit){{ $habit->notes }}@else{{ old('notes') }}@endisset</textarea>
    @error('notes')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    {{-- Buttons --}}
    <a href="{{ route('habits') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>
    