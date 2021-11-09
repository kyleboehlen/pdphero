{{-- Elapsed time --}}
<p class="elapsed-time">
    <span class="green">{{ $elapsed_years }}</span>Y&nbsp;
    <span class="green">{{ $elapsed_months }}</span>M&nbsp;
    <span class="green">{{ $elapsed_days }}</span>D&nbsp;
    <span id="elapsed-hours-{{ $uuid }}" class="green">{{ $elapsed_hours }}</span>H&nbsp;
    <span id="elapsed-minutes-{{ $uuid }}" class="green">{{ $elapsed_minutes }}</span>M&nbsp;
    <span id="elapsed-seconds-{{ $uuid }}" class="green">{{ $elapsed_seconds }}</span>S&nbsp;
</p>

@push('scripts')
    <script>
        setInterval(() => {
            var uuid = '{{ $uuid }}';
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