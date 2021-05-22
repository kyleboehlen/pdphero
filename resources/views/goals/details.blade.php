@extends('layouts.app')

@section('template')
    {{-- Header --}}
    <x-app.header title="Goals" />

    {{-- Side Nav --}}
    <x-goals.nav :show="$nav_show" :goal="$goal" />

    <div class="app-container goal-details">
        {{-- Goal Category --}}
        <p class="goal-category">{{ !is_null($goal->category) ? $goal->category->name : 'Uncategorized' }}</p>

        {{-- Goal title --}}
        <h2>{{ $goal->name }}</h2>

        {{-- Goal Dates --}}
        @if($goal->achieved)
            <p class="goal-dates">Achieved On {{ \Carbon\Carbon::parse($goal->updated_at)->setTimezone(\Auth::user()->timezone ?? 'America/Denver')->format('n/j/y') }}</p>
        @else
            @if(!is_null($goal->end_date))
                <p class="goal-dates">
                    @if(!is_null($goal->start_date))
                        {{ \Carbon\Carbon::parse($goal->start_date)->format('n/j/y') }} - 
                    @else
                        By:
                    @endif
                    {{ \Carbon\Carbon::parse($goal->end_date)->format('n/j/y') }}
                </p>
            @endif
        @endif

        {{-- Nav dropdown --}}
        <select id="goal-nav-dropdown">
            @foreach($dropdown_nav as $key => $value)
                <option value="{{ $key }}" @if($selected_dropdown == $key) selected @endif>{{ $value }}</option>
            @endforeach
        </select><br/>

        {{-- Goal Details --}}
        @if(array_key_exists('details', $dropdown_nav))
            <div id="goal-details-div" class="goal-nav-div">
                {{-- Goal Image --}}
                <x-goals.image :goal="$goal" />

                {{-- Goal Reason --}}
                <br/>
                <h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reason&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>
                @foreach(explode(PHP_EOL, $goal->reason) as $line)
                    <p>{{ $line }}</p>
                @endforeach

                {{-- Goal Notes --}}
                @if(!is_null($goal->notes))
                    <h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>
                    @foreach(explode(PHP_EOL, $goal->notes) as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                @endif
                <div class="details-clear-all"></div>
            </div><br/>
        @endif

        {{-- Goal Progress --}}
        @if(array_key_exists('progress', $dropdown_nav))
            <h3 class="goal-section-title" id="progress-section-title">Progress</h3>
            <div id="goal-progress-div" class="goal-nav-div hidden">
                {{-- Goal progress bar --}}
                <br/><br/><br/>
                @if($goal->type_id == $type::MANUAL_GOAL)
                    <p class="manual-progress">{{ $goal->manual_completed }} out of {{ $goal->custom_times }} completed
                @endif
                <x-app.progress-bar :percent="$goal->progress" /><br/>

                {{-- Status label/bar --}}
                <div class="status" title="{{ $goal->status->desc }}">
                    <p class="label {{ $goal->status->class }}">{{ $goal->status->name }}</p>
                    @switch($goal->status_id)
                        @case($status::TBD)
                            <img src="{{ asset('icons/goal-status-bar-tbd.png') }}" />
                            @break
                        @case($status::LAGGING)
                            <img src="{{ asset('icons/goal-status-bar-lagging.png') }}" />
                            @break
                        @case($status::ON_TRACK)
                            <img src="{{ asset('icons/goal-status-bar-ontrack.png') }}" />
                            @break
                        @case($status::AHEAD)
                            <img src="{{ asset('icons/goal-status-bar-ahead.png') }}" />
                            @break
                        @case($status::COMPLETED)
                            <img src="{{ asset('icons/goal-status-bar-completed.png') }}" />
                            @break
                    @endswitch
                </div>
            </div><br/>
        @endif
        
        {{-- Goal Action Plan --}}
        @if(array_key_exists('action-plan', $dropdown_nav))
            <h3 class="goal-section-title" id="action-plan-section-title">Action Plan</h3>
            <div id="goal-action-plan-div" class="goal-nav-div hidden">
                @if($goal->type_id == $type::ACTION_AD_HOC)
                    @foreach($goal->getAdHocArray() as $array)
                        <p class="ad-hoc-label">{{ $array['start_date'] }} - {{ $array['end_date'] }}</p>
                        <p class="ad-hoc-label"> {{ $array['action_items']->count() }} out of {{ $goal->custom_times }} scheduled </p>
                        <hr class="ad-hoc-label">
                        @foreach($array['action_items'] as $action_item)
                            <x-goals.action-item :item="$action_item" />
                        @endforeach
                        @for($i = $array['action_items']->count(); $i < $goal->custom_times; $i++)
                            <x-goals.empty-action-item :goal="$goal" route="goals.view.goal" />
                        @endfor
                        <br/><br/>
                    @endforeach
                @else
                    @if($goal->actionItems->count() > 0)
                        @foreach($goal->actionItems as $action_item)
                            <x-goals.action-item :item="$action_item" />
                        @endforeach
                    @else
                        <x-goals.empty-action-item :goal="$goal" />
                    @endif
                @endif
            </div><br/><br/>
        @endif

        {{-- Sub Goals --}}
        @if(array_key_exists('sub-goals', $dropdown_nav))
            <h3 class="goal-section-title">Sub Goals</h3>
            <div id="goal-sub-goals-div" class="goal-nav-div hidden">
                <br/>
                @foreach($goal->subGoals as $sub_goal)
                    @if($goal->subGoals->count() < 3)
                        <x-goals.goal :goal="$sub_goal" class="align-start" />
                    @else
                        <x-goals.goal :goal="$sub_goal" />
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Ad Hoc List --}}
        @if(array_key_exists('ad-hoc-list', $dropdown_nav))
            <h3 class="goal-section-title">Ad Hoc List</h3>
            <div id="goal-ad-hoc-list-div" class="goal-nav-div hidden">
                @if($goal->adHocItems->count() > 0)
                    @foreach($goal->adHocItems as $ad_hoc_item)
                        <x-goals.ad-hoc-list-item :item="$ad_hoc_item" />
                    @endforeach
                @else
                    <x-goals.empty-ad-hoc-list-item :goal="$goal" />
                @endif
            </div><br/><br/>
        @endif

        {{-- Manual Goal progress popup --}}
        @if(strpos($nav_show, 'update-manual-progress') !== false)
            @push('scripts')
                <div class="overlay-hide" id="manual-progress-container">
                    {{-- Goal Header --}}
                    <h2>Update {{ $goal->name }} Progress</h2><br/><br/><br/>
                    
                    {{-- Update progress form --}}
                    <form action="{{ route('goals.update.manual-progress', ['goal' => $goal->uuid]) }}" method="POST">
                        @csrf

                        <p>Completed</p>
                        <div class="manual-completed-container">
                            <img id="manual-completed-decrement" src="{{ asset('icons/minus-white.png') }}" />
                            <input type="number" name="manual-completed" value={{ $goal->manual_completed }} id="manual-completed-input" min="0" max="{{ $goal->custom_times + config('goals.manual_goal_buffer') }}" />
                            <img id="manual-completed-increment" src="{{ asset('icons/plus-white.png') }}" />
                        </div>
                        @error('manual-completed')
                            <script>
                                sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                            </script>
                        @enderror
                        <p>out of {{ $goal->custom_times }} required</p><br/><br/><br/>

                        {{-- Cancel/Save buttons --}}
                        <div class="buttons-container">
                            <button type="button" class="swal2-confirm swal2-styled manual-progress-updater-button overlay-cancel-button">Cancel</button>
                            <button type="submit" class="swal2-confirm swal2-styled manual-progress-updater-button">Save</button>
                        </div>
                    </form>
                </div>

                <script>
                    $(document).ready(function(){
                        $('#show-manual-progress').click(function(){
                            $('.overlay').show();
                            $('#manual-progress-container').show();
                        });
                    });
                </script>
            @endpush
        @endif

        {{-- Shift dates popup --}}
        @if(strpos($nav_show, 'shift-date') !== false)
            @push('scripts')
                <div class="overlay-hide" id="shift-dates-container">
                    {{-- Goal Header --}}
                    <h2>Shift Deadlines For All @if($goal->type_id == $type::PARENT_GOAL){{ 'Sub-Goals and' }}@endif Action Items</h2><br/><br/><br/>
                    
                    {{-- Shift dates form --}}
                    <form action="{{ route('goals.shift-dates', ['goal' => $goal->uuid]) }}" method="POST">
                        @csrf

                        <p>Shift by:</p>
                        <div class="shift-days-container">
                            <img id="shift-days-decrement" src="{{ asset('icons/minus-white.png') }}" />
                            <input type="number" name="shift-days" value="1" id="shift-days-input" min="-365" max="365" />
                            <img id="shift-days-increment" src="{{ asset('icons/plus-white.png') }}" />
                        </div>
                        @error('shift-days')
                            <script>
                                sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                            </script>
                        @enderror
                        <p>days</p><br/><br/><br/>

                        {{-- Cancel/Save buttons --}}
                        <div class="buttons-container">
                            <button type="button" class="swal2-confirm swal2-styled shift-dates-updater-button overlay-cancel-button">Cancel</button>
                            <button type="submit" class="swal2-confirm swal2-styled shift-dates-updater-button">Save</button>
                        </div>
                    </form>
                </div>

                <script>
                    $(document).ready(function(){
                        $('#show-shift-dates').click(function(){
                            $('.overlay').show();
                            $('#shift-dates-container').show();
                        });
                    });
                </script>
            @endpush
        @endif
    </div>

    {{-- Navigation Footer --}}
    <x-app.footer highlight="goals" />
@endsection