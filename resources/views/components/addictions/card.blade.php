<div class="addiction">
    {{-- Method image --}}
    @if($addiction->method_id == $method::ABSTINENCE)
        <img class="method-img" src="{{ asset('icons/addiction-milestone.png') }}" />
    @elseif($addiction->method_id == $method::MODERATION)
        <img class="method-img" src="{{ asset('icons/addiction-usage.png') }}" />
    @endif

    {{-- Title/link --}}
    <div class="info">
        <a href="{{ route('addiction.details', ['addiction' => $addiction->uuid]) }}">
            <h2>{{ $addiction->name }}</h2>
        </a>

        {{-- Milestone/usage info --}}
        @if($addiction->method_id == $method::ABSTINENCE)
            <span class="milestone">
                <p class="{{ is_null($milestone_name) ? 'yellow' : 'green' }}">{{ $milestone_name ?? 'Pending Milestone'}}</p>
            </span>
        @elseif($addiction->method_id == $method::MODERATION)
            <span class="usage">
                <p><span class="color {{ $usage_color }}"> {{ $usage }}</span> / {{ $addiction->moderated_amount }}</p>
            </span>
        @endif

        {{-- Elapsed time --}}
        <p class="elapsed-time">
            <span class="green">{{ $elapsed_years }}</span>Y&nbsp;
            <span class="green">{{ $elapsed_months }}</span>M&nbsp;
            <span class="green">{{ $elapsed_days }}</span>D&nbsp;
            <span id="elapsed-hours-{{ $addiction->uuid }}" class="green">{{ $elapsed_hours }}</span>H&nbsp;
            <span id="elapsed-minutes-{{ $addiction->uuid }}" class="green">{{ $elapsed_minutes }}</span>M&nbsp;
            <span id="elapsed-seconds-{{ $addiction->uuid }}" class="green">{{ $elapsed_seconds }}</span>S&nbsp;
        </p>

        @push('scripts')
            <script>
                setInterval(() => {
                    var uuid = '{{ $addiction->uuid }}';
                    var hours = $('#elapsed-hours-' + uuid).text();
                    var minutes = $('#elapsed-minutes-' + uuid).text();
                    var seconds = $('#elapsed-seconds-' + uuid).text();

                    seconds++;
                    if(seconds == 60)
                    {
                        seconds = 0;
                        minutes++;
                    }

                    if(minutes == 60)
                    {
                        minutes = 0;
                        hours++;
                    }

                    if(hours == 24)
                    {
                        document.location.reload();
                    }

                    $('#elapsed-hours-' + uuid).text(hours);
                    $('#elapsed-minutes-' + uuid).text(minutes);
                    $('#elapsed-seconds-' + uuid).text(seconds);
                }, 1000);
            </script>
        @endpush
    </div>
</div>