<div class="history-toggle-container">
    @foreach($habit_history_array as $day_key => $history)
        <div class="history-toggle-item">
            @foreach(explode('|', $history['label']) as $label)
                <p>{{ $label }}</p>
            @endforeach
            <div class="toggle-history {{ $history['classes'] }}"
                id="toggle-{{ $habit->uuid }}-{{ $day_key }}">
            </div>
        </div>

        {{-- Pop up history tracker stuffs--}}
        @push('scripts')
            <div class="history-updater-container overlay-hide" id="toggle-{{ $habit->uuid }}-{{ $day_key }}-updater">
                {{-- Date header --}}
                <h2>{{ $history['carbon']->format('l, F jS') }}</h2>
                
                @if($history['required'])
                    @if($habit->times_daily == 1)
                        <h3 class="required">Required</h3>
                    @else
                        <h3 class="required">Required {{ $habit->times_daily }} times</h3>
                    @endif
                @else
                    <h3 class="not-required">Not Required</h3>
                @endif

                {{-- If we're creating the form, and the day is either today or in the past --}}
                @if($history['carbon']->isToday() || $history['carbon']->lessThan(\Carbon\Carbon::now()))
                    @if($create_update_form)
                        <form class="history-updater" action="{{ route('habits.history', ['habit' => $habit->uuid]) }}" method="POST">
                            @csrf

                            <input type="hidden" name="day" value="{{ $history['carbon']->format('Y-m-d') }}" />
                            @error('day')
                                <script>
                                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                                </script>
                            @enderror

                            <p class="history-status-label" id="history-status-{{ $habit->uuid }}-{{ $day_key }}-label">
                                @switch($history['status'])
                                    @case($type::COMPLETED)
                                    @case($type::PARTIAL)
                                        Completed
                                        @break

                                    @case($type::MISSED)
                                        Missed
                                        @break

                                    @case($type::SKIPPED)
                                        Skipped
                                        @break

                                    @default {{-- Including $type::TBD --}}
                                        To Be Determined
                                        @break
                                @endswitch
                            </p>
                            
                            <div class="status-container">
                                <input class="history-status-checkbox completed-checkbox" id="history-status-{{ $habit->uuid }}-{{ $day_key }}-completed" type="checkbox" name="status-completed" value="{{ $type::COMPLETED }}" @if($history['status'] == $type::COMPLETED || $history['status'] == $type::PARTIAL) checked @endif />
                                @error('status-completed')
                                    <script>
                                        sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                                    </script>
                                @enderror

                                <input class="history-status-checkbox skipped-checkbox" id="history-status-{{ $habit->uuid }}-{{ $day_key }}-skipped" type="checkbox" name="status-skipped" value="{{ $type::SKIPPED }}" @if($history['status'] == $type::SKIPPED) checked @endif />
                                @error('status-skipped')
                                    <script>
                                        sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                                    </script>
                                @enderror

                                @if($history['required'])
                                    <input class="history-status-checkbox missed-checkbox" id="history-status-{{ $habit->uuid }}-{{ $day_key }}-missed" type="checkbox" name="status-missed" value="{{ $type::MISSED }}" @if($history['status'] == $type::MISSED) checked @endif />
                                    @error('status-missed')
                                        <script>
                                            sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                                        </script>
                                    @enderror
                                @endif
                            </div><br/>

                            <textarea id="history-status-{{ $habit->uuid }}-{{ $day_key }}-notes" name="notes" placeholder="Notes">{{ $history['notes'] }}</textarea>
                            @error('notes')
                                <script>
                                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                                </script>
                            @enderror
                            <br/><br/>

                            <div class="history-times-container" id="history-status-{{ $habit->uuid }}-{{ $day_key }}-times-container" @if($history['status'] == $type::MISSED || $history['status'] == $type::SKIPPED) style="display: none;" @endif>
                                <img class="history-times-decrement" id="history-times-{{ $habit->uuid }}-{{ $day_key }}-decrement" src="{{ asset('icons/minus-white.png') }}" />
                                <input type="number" name="times" value={{ $history['times'] > 1 ? $history['times'] : 1 }} id="history-times-{{ $habit->uuid }}-{{ $day_key }}-input" min="1" max="{{ $habit->times_daily }}" @if($history['status'] == $type::MISSED || $history['status'] == $type::SKIPPED) disabled @endif />
                                <img class="history-times-increment" id="history-times-{{ $habit->uuid }}-{{ $day_key }}-increment" src="{{ asset('icons/plus-white.png') }}" />
                            </div><br/>
                            @error('times')
                                <script>
                                    sweetAlert('Error', 'error', '#d12828', '{{ $message }}');
                                </script>
                            @enderror

                            {{-- Cancel/Save buttons --}}
                            <div class="buttons-container">
                                <button type="button" class="swal2-confirm swal2-styled history-updater-button overlay-cancel-button">Cancel</button>
                                <button type="submit" class="swal2-confirm swal2-styled history-updater-button">Save</button>
                            </div>
                        </form>
                    @else
                        <div class="auto-out-of">
                            <p><b>Completed: </b><i>{{ $history['times'] }} times</i></p>
                            <div class="buttons-container">
                                <button type="button" class="swal2-confirm swal2-styled history-updater-button overlay-cancel-button">Okay</button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="auto-out-of">
                        <p><i>Future Date</i></p>
                        <div class="buttons-container">
                            <button type="button" class="swal2-confirm swal2-styled history-updater-button overlay-cancel-button">Okay</button>
                        </div>
                    </div>
                @endif
            </div>

            <script>
                $(document).ready(function(){
                    $('#toggle-{{ $habit->uuid }}-{{ $day_key }}').click(function(){
                        $('.overlay').show();
                        $('#toggle-{{ $habit->uuid }}-{{ $day_key }}-updater').show();
                    });
                });
            </script>
        @endpush
    @endforeach
</div>