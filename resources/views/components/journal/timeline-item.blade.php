<span class="time-spacer summary-hide {{ $class }}"></span>
<span class="time-label summary-hide {{ $class }}">{{ $time }}</span>
<div class="summary summary-hide summary-timeline-item {{ $class }} @isset($priority) todo-item @endisset">
    <ul class="timeline-item" style="list-style-image: url('{{ url(asset('icons/check-bullet-white.png')) }}');">
        <li><b>{{ $bold_content }}</b> <i>@if(!is_null($link))<a class="preview" href="{{ $link }}">@endif{{ $italic_content }}@if(!is_null($link))</a>@endif</i></li>
    </ul>
    @isset($priority)
        <div class="priority {{ $priority }}"></div>
    @endisset
</div>