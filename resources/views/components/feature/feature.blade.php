<div class="feature">
    <div class="text">
        <b>{{ $feature->name }}: </b>
        <a class="preview" href="{{ route('feature.details', ['feature' => $feature]) }}">
            <i>
                {{ $feature->desc }}
            </i>
        </a>
    </div>
    <div class="vote {{ $class }}"></div>
</div>